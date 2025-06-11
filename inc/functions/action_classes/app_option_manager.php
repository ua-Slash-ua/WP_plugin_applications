<?php
use sl_app\ApplicationOptionManager;
$appManager = new ApplicationOptionManager();

function sl_add_option($optionKey,$optionValue,$optionParentId = 0 ): int|false
{
    global $appManager;
    return $appManager->createAppOption($optionKey,$optionValue,$optionParentId);
}

function sl_get_option($optionKey, int $optionId = null, int $optionParentId = null): array|string|null
{
    global $appManager;

    $result = $appManager->getAppOption($optionKey, $optionId, $optionParentId);

    // Якщо null або рядок — можливо розпарсити
    if (is_string($result)) {
        $decoded = json_decode($result, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $result;
    }

    // Якщо масив — перевіряємо кожен елемент
    if (is_array($result)) {
        return array_map(function ($item) {
            $decoded = json_decode($item, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $item;
        }, $result);
    }

    return $result;
}


function sl_remove_option(string $optionKey = null, string $optionValue = null, int $optionId = null,  string $optionParentId = null): int
{
    global $appManager;

    return $appManager->removeAppOption($optionKey,$optionId,$optionValue,$optionParentId);
}