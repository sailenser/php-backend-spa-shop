<?php

    include_once('_fn/system.php');
    checkCORS();
    randomExit(0);
    $id = (int)$_GET['id'] ?? null;

    if(!$id || !file_exists("_db/products.data")){
        return false;
    }

    $products = json_decode(file_get_contents("_db/products.data"), true);

    $result = null;
    foreach ($products as $item) {
        if ($item['id'] === $id) {
            $result = $item;
            break;
        }
    }

    if ($result) {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode("Product not found", JSON_UNESCAPED_UNICODE);
    }
?>