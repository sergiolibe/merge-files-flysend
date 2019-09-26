<?php

function dd($var)
{
    echo "<pre>";
    print_r($var);
    exit;
}

// $files = array_slice(scandir('mergeFiles'), 2);
$path = 'mergeFiles';
// dd($files);

$files2 = array_slice(scandir('mergeFiles/dropbox'), 2);
$path2 = 'mergeFiles/dropbox';
// dd($files2);

$cleanContacts = [];
$errors = '';
$phones = [];
$phoneArray = [];
$j = 0;
foreach ($files2 as $file) {
    ini_set('auto_detect_line_endings', TRUE);
    $f = fopen($path2 . '/' . $file, "r");
    $i = 0;
    while (($row = fgetcsv($f, 10000, ",")) !== FALSE) {
        $row[0] = strlen($row[0]) == 10 ? '1' . $row[0] : $row[0];
        $row[25] = $row[0] . ' en fila ' . $i . ' , en archivo ' . $file;
        array_push($cleanContacts, $row);
        array_push($phones, $row[0]);
        $phoneArray[$row[0]] = $row;
        $i++;
        $j++;
    }
    fclose($f);
    ini_set('auto_detect_line_endings', FALSE);
}
// dd($errors);
// dd([$j, sizeof($cleanContacts), sizeof(array_unique($phones)), sizeof($phoneArray)]);

$originalContacts = [];
$phoneArray = [];
$phones = [];
$errors2 = '';
$i = 0;
$j = 0;
ini_set('auto_detect_line_endings', TRUE);
$emptyArray = [''];
$f = fopen($path . '/doca.csv', "r");
$repeatedPhones = [];
while (($row = fgetcsv($f, 10000, ";")) !== FALSE) {
    // dd($row);
    $row[1] = str_replace(['(', ')', '-', ' ', '+', ',', '.', '/', '=', '–'], '', $row[1]);
    $row[1] = strlen($row[1]) == 10 ? '1' . $row[1] : $row[1];
    if (strlen($row[1]) != 11) {
        $errors2 .= $row[1] . ' en fila ' . $i . ' , en archivo ' . $file . ' <br>';
    } elseif (strlen($row[1]) == 11) {
        array_push($originalContacts, $row);
        array_push($phones, $row[1]);
        if (isset($phoneArray[$row[1]])) {
            $repeatedPhones[$row[1]] = isset($repeatedPhones[$row[1]]) ? $repeatedPhones[$row[1]] + 1 : 1;
            if (sizeof(array_diff($phoneArray[$row[1]], $emptyArray)) == 16) {
                $phoneArray[$row[1]] = $row;
                // $j++;
            }
        } else {
            $phoneArray[$row[1]] = $row;
            // if (sizeof(array_diff($phoneArray[$row[9]], $emptyArray)) == 16) echo $row[9] . '<br>';
        }
    }

    $i++;
}
fclose($f);
ini_set('auto_detect_line_endings', FALSE);

// dd($phoneArray);
// dd(array_keys($phoneArray));
// dd($errors2);
// dd($repeatedPhones);
$emptyArray = [''];
$keys = array_keys($phoneArray);

// dd([$i, sizeof($originalContacts), sizeof(array_unique($phones)), sizeof($phoneArray), array_sum($repeatedPhones), sizeof($repeatedPhones)]);
// dd(['N Filas' => $i,'N Telefonos buen formato' => sizeof($originalContacts),'N Telefonos Unicos' => sizeof($phoneArray),'N Total de Telefonos Repetidos' => array_sum($repeatedPhones),'N Telefonos Repetidos Unicos' => sizeof($repeatedPhones)]);








// $columns = explode(',', 'Order Id,Bill First,Bill Last,ADDRESS 1,ADDRESS 2,CITY,ST,ZIP,COUNTRY,PHONE,EMAIL,Sub Total,Order Total,Date of Sale,Time of Sale,Payment');
// dd($columns);


// $columns = explode(',', 'Order Id,Bill First,Bill Last,ADDRESS 1,ADDRESS 2,CITY,ST,ZIP,COUNTRY,PHONE,EMAIL,Sub Total,Order Total,Date of Sale,Time of Sale,Payment,Type of Line,Carrier ID,Carrier Name,Status');
//16=>Type of Line   17=>Carrier ID  18=>Carrier Name   19=>Status
// dd($columns);

// $columns = explode(',', 'Firstname,Phone,Campaign,Type of Line,Carrier ID,Carrier Name,Status');
$i = 0;
$keys = [];
$match = [];
foreach ($cleanContacts as $cleanContact) {

    if (isset($phoneArray[$cleanContact[0]])) {
        $match[$cleanContact[0]] = '';
        // $keys[$cleanContact[1]] = isset($keys[$cleanContact[1]]) ? $keys[$cleanContact[1]] + 1 : 1;
        // $keys[$cleanContact[1]] = isset($keys[$cleanContact[1]]) ?: $cleanContact[25];
        // dd($phoneArray);
        // dd($cleanContact);
        $phoneArray[$cleanContact[0]][3] = $cleanContact[4];
        // $phoneArray[$cleanContact[0]][16] = $cleanContact[1] == 'n' ? 'Landline' : 'Mobile';

        $phoneArray[$cleanContact[0]][4] = 'D247_INVALID_PHONE';
        if ($cleanContact[1] == 'y') $phoneArray[$cleanContact[0]][4] = 'Mobile';
        if ($cleanContact[1] == 'n') $phoneArray[$cleanContact[0]][4] = 'Landline';
        $phoneArray[$cleanContact[0]][5] = $cleanContact[2];
        $phoneArray[$cleanContact[0]][6] = $cleanContact[3];
        // $phoneArray[$cleanContact[0]][19] = $cleanContact[5] == 'OK' ? 'OK' : '247';
        $phoneArray[$cleanContact[0]][7] = $cleanContact[5] == 'OK' ? 'OK' : '';
        if (strlen($cleanContact[5]) > 0 && $cleanContact[5] != 'OK') $phoneArray[$cleanContact[0]][7] = 'D247_INVALID_PHONE';
    }
}
// dd(sizeof($match));
// dd($phoneArray);
// dd(array_keys($phoneArray));
// dd($keys);
// dd($j);
echo 'Firstname,Phone,Campaign,Country,Type of Line,Carrier ID,Carrier Name,Status';
// echo 'Order Id,Bill First,Bill Last,ADDRESS 1,ADDRESS 2,CITY,ST,ZIP,COUNTRY,PHONE,EMAIL,Sub Total,Order Total,Date of Sale,Time of Sale,Payment,Type of Line,Carrier ID,Carrier Name,Status';
echo '<br>';
foreach ($phoneArray as $phone => $phoneData) {
    // echo $phone . ' -> ';
    echo implode(',', $phoneData);
    // echo $phone;
    echo '<br>';
}
