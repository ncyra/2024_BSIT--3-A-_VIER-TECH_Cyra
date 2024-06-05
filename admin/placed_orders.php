<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:admin_login.php');
}

if(isset($_POST['update_payment'])){
   $order_id = $_POST['order_id'];
   $payment_status = $_POST['payment_status'];
   $order_status = isset($_POST['order_status']) ? $_POST['order_status'] : null; // Ensure order_status is set
   
   // Check if order_status is not null
   if ($order_status !== null && $payment_status !== null) {
      $update_status = $conn->prepare("UPDATE `orders` SET payment_status = ?, order_status = ? WHERE id = ?");
      $update_status->execute([$payment_status, $order_status, $order_id]);
      $message[] = 'Order and payment status updated!';
   } else {
      $message[] = 'Please select both payment status and order status.';
   }
}

if(isset($_GET['archive'])){
   $archive_id = $_GET['archive'];
   $archive_order = $conn->prepare("UPDATE `orders` SET archived = 1 WHERE id = ?");
   $archive_order->execute([$archive_id]);
   header('location:archived_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>placed orders</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<?php include '../components/admin_header.php' ?>

<section class="placed-orders">
   <h1 class="heading">placed orders</h1>
   <div class="box-container">
   <?php
      $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE archived = 0");
      $select_orders->execute();
      if($select_orders->rowCount() > 0){
         while($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p> user id : <span><?= $fetch_orders['user_id']; ?></span> </p>
      <p> placed on : <span><?= $fetch_orders['placed_on']; ?></span> </p>
      <p> name : <span><?= $fetch_orders['name']; ?></span> </p>
      <p> email : <span><?= $fetch_orders['email']; ?></span> </p>
      <p> number : <span><?= $fetch_orders['number']; ?></span> </p>
      <p> address : <span><?= $fetch_orders['address']; ?></span> </p>
      <p> total products : <span><?= $fetch_orders['total_products']; ?></span> </p>
      <p> total price : <span>₱<?= $fetch_orders['total_price']; ?></span> </p>
      <p> payment method : <span><?= $fetch_orders['method']; ?></span> </p>
      <form action="" method="POST">
         <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
         <select name="payment_status" class="drop-down" required>
            <option value="" selected disabled><?= $fetch_orders['payment_status']; ?></option>
            <option value="pending">pending</option>
            <option value="completed">completed</option>
         </select>
         <select name="order_status" class="drop-down" required>
            <option value="" selected disabled><?= $fetch_orders['order_status']; ?></option>
            <option value="to ship">to ship</option>
            <option value="to receive">to receive</option>
            <option value="delivered">delivered</option>
            <option value="cancelled">cancelled</option>
         </select>
         <div class="flex-btn">
            <input type="submit" value="update" class="btn" name="update_payment">
            <a href="placed_orders.php?archive=<?= $fetch_orders['id']; ?>" class="delete-btn" onclick="return confirm('archive this order?');">archive</a>
         </div>
      </form>
   </div>
   <?php
      }
   } else {
      echo '<p class="empty">no orders placed yet!</p>';
   }
   ?>
   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>
