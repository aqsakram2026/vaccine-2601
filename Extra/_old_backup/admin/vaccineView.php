<?php
include('database.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    td{
        padding:1em;
    }
</style>
<body>
    <table border="1" style="border-collapse: collapse; ">
        <thead>
            <th>Stock ID</th>
            <th>Hospital Name</th>
            <th>Vaccine Name</th>
            <th>Quantity</th>
            <th>Last Update</th>
            
        </thead>

        <?php
        $query = "SELECT 
    hospital_vaccine_stock.stock_id, 
    hospitals.hospital_name, 
    vaccines.Vaccine_Name, 
    hospital_vaccine_stock.quantity, 
    hospital_vaccine_stock.updated_at
FROM hospital_vaccine_stock
JOIN hospitals ON hospital_vaccine_stock.hospital_id = hospitals.id
JOIN vaccines ON hospital_vaccine_stock.vaccine_id = vaccines.Vaccine_ID
 ";
        $run = mysqli_query($connect, $query);
        // print_r(mysqli_fetch_assoc($run));
        while ($result = mysqli_fetch_assoc($run)) {


        ?>
            <tr>
                <td><?= $result['stock_id']; ?></td>
                <td><?= $result['hospital_name']; ?></td>
                <td><?= $result['Vaccine_Name']; ?></td>
                <td><?= $result['quantity']; ?></td>
                <td><?= $result['updated_at']; ?></td>
               
                
                
            </tr>
        <?php
        }
        ?>
    </table>
</body>

</html>