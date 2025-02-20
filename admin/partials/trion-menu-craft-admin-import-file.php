<?php 

$plugin_dir = plugin_dir_path(__FILE__) . 'upload-analytic-script/import-analytic-admin-analytic-import.php';

require($plugin_dir);


?>
<!--******** Import excel sheet data  ***********-->
<div class="container">
    <h1 id="heading1">Subir archivo</h1>
    <form method="post" enctype="multipart/form-data">
        <div class="variants">
            <div class='file file--uploading'>
                <label for='input-file'>
                <i class="material-icons">cloud_upload</i>Subiendo
                </label>
                <input type='file' name="excel_file" id='input-file' />
            </div>
        </div>
        <div class="import_btn">
            <input type="submit" name="import" value="Entregar" id="import_btn_data"/>
        </div>
    </form>
</div>

<?php
// if (isset($_POST["import"])) 
// {
//     if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == 0) 
//     {
//         $file = $_FILES["csv_file"]["tmp_name"];

//         if (($handle = fopen($file, "r")) !== false) 
//         {
//             $csvData = array();

//             while (($data = fgetcsv($handle, 1000, ",")) !== false) 
//             {
//                 $csvData[] = $data;
//             }

//             fclose($handle);

//             $headers = array_shift($csvData); // Remove the header row from the data array

//             // Define the batch size
//             $batchSize = 100;

//             // Get the total number of records
//             $totalRecords = count($csvData);

//             // Open the log file for writing
//             $logFile = fopen(dirname(__FILE__) . '/import_log.txt', 'w');

//             // Process the CSV data in batches
//             $counter = 0;
//             $nextCounter = $batchSize;
//             $oldCounter = 0;

//             while ($counter < $totalRecords) 
//             {
//                 $batchData = array_slice($csvData, $counter, $batchSize);

//                 functioncall($batchData, $headers, $logFile, $counter);

//                 $counter += $batchSize;

//                 // Check if the counter exceeds the total number of records
//                 if ($counter >= $totalRecords) {
//                     break; // Exit the loop if the counter exceeds the total number of records
//                 }

//                 $logMessage = 'Batch processed. Current Counter: ' . $counter . PHP_EOL;

//                 //echo $logMessage;
//                 //fwrite($logFile, $logMessage);
//             }

//             // Close the log file
//             fclose($logFile);
//         }
//     }
// }