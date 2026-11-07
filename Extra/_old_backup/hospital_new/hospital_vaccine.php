<?php
session_start();
include('hospital_db.php');  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST">
        <h1><?= $_SESSION['hospital_name'] ?></h1>
        Hospital ID :<input type="text" value="<?= $_SESSION['hospital_id'] ?>" readonly><br>
        
     <label for="sizesSelectSmall" class="form-label">select</label>
  <select class="form-control form-control-sm" id="sizesSelectSmall" name="vaccine">
      <option>Open this select menu</option>
        <?php
            $query = "SELECT * FROM vaccines ";
            $run = mysqli_query($connect,$query);
            
            while ($result = mysqli_fetch_assoc($run)) {
                ?>
            <option value="<?=$result['Vaccine_ID']?>"><?=$result['Vaccine_Name']?></option>
             
            <?php
            }
            ?>
             </select><br>
        <!-- vaccine ID :<input type="text" value="<?= $_SESSION['hospital_id'] ?>" readonly> -->
         Quantity :<input type="text" name="quantity" >
         <button name="btn">done</button>
    </form>
</body>
</html>
<?php 
      if($_SERVER['REQUEST_METHOD'] == 'POST'){
          $hospital_id = $_SESSION['hospital_id'];
         $vaccine_id = $_POST['vaccine'];
         $quantity = $_POST['quantity'];

         $query2 = "insert INTO hospital_vaccine_stock(hospital_id,vaccine_id,quantity)
VALUES($hospital_id,$vaccine_id,$quantity)";
    mysqli_query($connect,$query2);
      }
 ?>