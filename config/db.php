<?php

define("FIREBASE_URL", "https://console.firebase.google.com/u/0/project/telefonia-d8f6d/database/telefonia-d8f6d-default-rtdb/data/~2F");

// función para GET
function firebase_get($path) {
    $url = FIREBASE_URL . $path . ".json";
    return json_decode(file_get_contents($url), true);
}

// función para POST (crear)
function firebase_post($path, $data) {
    $url = FIREBASE_URL . $path . ".json";

    $options = [
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/json",
            "content" => json_encode($data)
        ]
    ];

    return json_decode(file_get_contents($url, false, stream_context_create($options)), true);
}

// función para PUT (editar)
function firebase_put($path, $data) {
    $url = FIREBASE_URL . $path . ".json";

    $options = [
        "http" => [
            "method"  => "PUT",
            "header"  => "Content-Type: application/json",
            "content" => json_encode($data)
        ]
    ];

    return json_decode(file_get_contents($url, false, stream_context_create($options)), true);
}


function firebase_delete($path) {
    $url = FIREBASE_URL . $path . ".json";

    $options = [
        "http" => [
            "method" => "DELETE"
        ]
    ];

    return file_get_contents($url, false, stream_context_create($options));
}