<?php
header('Content-Type: application/json');
require_once '../Connection/connection.php';

$action = $_GET['action'] ?? '';

try {
    $db = new DatabaseConnection();
    $conn = $db->getConnection();
    
    switch ($action) { 
        case 'get_patient':
            get_patient($conn);
            break;
        case 'display_payment':
            display_payment($conn);
            break;
        case 'create_payment':
            create_payment($conn);
            break;
        case 'update_payment':
            update_payment($conn);
            break;
        case 'delete_payment':
            delete_payment($conn);
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
function get_patient($conn) {
  $stmt = $conn->query("
      SELECT 
          p.patient_id AS _id,
          p.full_name AS patient_name,
          COALESCE(SUM(v.charge), 0) AS total_charge
      FROM patients p
      LEFT JOIN appointments a ON p.patient_id = a.patient_id
      LEFT JOIN visits v ON a.appointment_id = v.appointment_id
      GROUP BY p.patient_id, p.full_name
  ");

  echo json_encode([
      'status' => 'success',
      'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
  ]);
}
function display_payment($conn) {
    $query = "
       SELECT 
            pa.payment_id,
            p.patient_id,
            p.full_name as patient_name,
            pa.amount,
            pa.paid_amount,
            pa.remainder,
            pa.payment_date,
            pa.status
        FROM payments  pa
         JOIN patients p ON pa.patient_id = p.patient_id
    ";
    
    $stmt = $conn->query($query);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function create_payment($conn) {
  $required = ['patient_id', 'amount', 'amount_paid', 'payment_date'];
  $data = [];

  foreach ($required as $field) {
      if (empty($_POST[$field])) {
          throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
      }
      $data[$field] = $_POST[$field];
  }

  $patient_id = $data['patient_id'];
  $amount = floatval($data['amount']);
  $new_paid = floatval($data['amount_paid']);
  $payment_date = $data['payment_date'];

  // ✅ Check if payment already exists
  $stmt = $conn->prepare("SELECT * FROM payments WHERE patient_id = ?");
  $stmt->execute([$patient_id]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($existing) {
      // ✅ Mar labaad lacag bixinayo → Update record
      $total_paid = floatval($existing['paid_amount']);
      $new_total = $total_paid + $new_paid;

      if ($new_total > $amount) {
          throw new Exception('Total paid amount cannot exceed the required amount.');
      }

      $remainder = $amount - $new_total;
      $status = ($remainder == 0) ? 'Paid' : 'Partial';

      $update = $conn->prepare("
          UPDATE payments 
          SET 
              paid_amount = ?, 
              remainder = ?, 
              payment_date = ?, 
              status = ? 
          WHERE patient_id = ?
      ");
      $success = $update->execute([$new_total, $remainder, $payment_date, $status, $patient_id]);

      if ($success) {
          echo json_encode([
              'status' => 'success',
              'message' => 'Payment updated successfully'
          ]);
      } else {
          throw new Exception('Failed to update existing payment');
      }

  } else {
      // ✅ First-time payment → INSERT new record
      if ($new_paid > $amount) {
          throw new Exception('Paid amount cannot be more than the total amount.');
      }

      $remainder = $amount - $new_paid;
      $status = ($remainder == 0) ? 'Paid' : 'Partial';

      $insert = $conn->prepare("
          INSERT INTO payments 
          (patient_id, amount, paid_amount, remainder, payment_date, status) 
          VALUES (?, ?, ?, ?, ?, ?)
      ");
      $success = $insert->execute([$patient_id, $amount, $new_paid, $remainder, $payment_date, $status]);

      if ($success) {
          echo json_encode([
              'status' => 'success',
              'message' => 'Payment recorded successfully'
          ]);
      } else {
          throw new Exception('Failed to record new payment');
      }
  }
}
// function update_payment($conn) {
//   // Hel ID
//   $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
  
//   $patient_id = $_POST['edit_patient_id'] ?? null;
//   $amount = $_POST['edit_amount'] ?? null;
//   $amount_paid = $_POST['edit_amount_paid'] ?? null;
//   $payment_date = $_POST['edit_payment_date'] ?? null;
//   // Status ma qaadan doono POST, waxaanu ku xisaabin doonaa
  
//   // Hubi fields required
//   if (!$id) throw new Exception('Payment ID is required');
//   if (!$patient_id) throw new Exception('Patient ID is required');
//   if ($amount === null) throw new Exception('Amount is required');
//   if ($amount_paid === null) throw new Exception('Amount Paid is required');
//   if (!$payment_date) throw new Exception('Payment Date is required');

//   // Hubi in amount_paid aanu ka badnayn amount
//   if ($amount_paid > $amount) {
//       throw new Exception('Amount paid cannot be greater than amount');
//   }

//   // Xisaabi remainder
//   $remainder = $amount - $amount_paid;

//   // Go'aami status
//   if ($remainder == 0) {
//       $status = 'Paid';
//   } elseif ($remainder > 0) {
//       $status = 'Partial';
//   } else {
//       $status = 'Unpaid'; // Haddii xaalad gaar ah jirto
//   }

//   // Update record
//   $stmt = $conn->prepare("
//       UPDATE payments SET
//           patient_id = ?,
//           amount = ?,
//           paid_amount = ?,
//           remainder = ?,
//           payment_date = ?,
//           status = ?
//       WHERE payment_id = ?
//   ");
  
//   $success = $stmt->execute([
//       $patient_id,
//       $amount,
//       $amount_paid,
//       $remainder,
//       $payment_date,
//       $status,
//       $id
//   ]);
  
//   if ($success) {
//       echo json_encode([
//           'status' => 'success',
//           'message' => 'Payments updated successfully'
//       ]);
//   } else {
//       throw new Exception('Failed to update Payments');
//   }
// }
function update_payment($conn) {
  // Accept both 'edit_id' and 'id' as the identifier
  $id = $_POST['edit_id'] ?? $_POST['id'] ?? null;
  
  $required = [
      'id' => $id,
      'patient_name' => $_POST['edit_patient_id'] ?? null,
      'amount' => $_POST['edit_amount'] ?? null,
      'amount_paid' => $_POST['edit_amount_paid'] ?? null,
      'payment_date' => $_POST['edit_payment_date'] ?? null,
      'status' => $_POST['edit_status'] ?? null
  ];
  
  // Validate required fields
  foreach ($required as $field => $value) {
      if (empty($value) && $value !== '0') {
          throw new Exception(ucfirst(str_replace('_', ' ', $field)) . ' is required');
      }
  }
  
  // Soo hel paid_amount hore
  $stmt = $conn->prepare("SELECT paid_amount FROM payments WHERE payment_id = ?");
  $stmt->execute([$id]);
  $existing = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$existing) {
      throw new Exception('Payment record not found');
  }
  
  $old_paid = (float)$existing['paid_amount'];
  $new_paid = (float)$required['amount_paid'];
  $amount = (float)$required['amount'];
  
  // Wadarta lacagta cusub ee la bixiyey (lacagtii hore + lacagta hadda la galinayo)
  $total_paid = $old_paid + $new_paid;
  
  // Hubi in lacagta cusub aysan ka badnayn amount
  if ($total_paid > $amount) {
      throw new Exception('Amount paid cannot be greater than total amount');
  }
  
  // Xisaabi remainder-ka
  $remainder = $amount - $total_paid;
  
  // Go'aami status-ka
  if ($remainder == 0) {
      $status = 'Paid';
  } elseif ($remainder > 0) {
      $status = 'Partial';
  } else {
      $status = 'Unpaid'; // Xaalad aan macquul ahayn hadii ay dhacdo
  }
  
  // Update record
  $stmt = $conn->prepare("
      UPDATE payments SET
          patient_id = ?,
          amount = ?,
          paid_amount = ?,
          remainder = ?,
          payment_date = ?,
          status = ?
      WHERE payment_id = ?
  ");
  
  $success = $stmt->execute([
      $required['patient_name'],
      $amount,
      $total_paid,
      $remainder,
      $required['payment_date'],
      $status,
      $id
  ]);
  
  if ($success) {
      echo json_encode([
          'status' => 'success',
          'message' => 'Payments updated successfully'
      ]);
  } else {
      throw new Exception('Failed to update Payments');
  }
}

function delete_payment($conn) {
    if (empty($_POST['id'])) {
        throw new Exception('Payment  ID is required');
    }
    
    $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
    $success = $stmt->execute([$_POST['id']]);
    
    if ($success) {
        echo json_encode([
            'status' => 'success',
            'message' => 'payment  deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete payment ');
    }
}
?>