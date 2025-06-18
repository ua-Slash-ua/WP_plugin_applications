<?php
use sl_app\ApplicationOptionManager;
$appOptionManager = new ApplicationOptionManager();

function sl_add_option($optionKey,$optionValue,$optionParentId = 0 ): int|false
{
    global $appOptionManager;
    return $appOptionManager->createAppOption($optionKey,$optionValue,$optionParentId);
}

function sl_get_option($optionKey, int $optionId = null, int $optionParentId = null): array|string|null
{
    global $appOptionManager;

    $result = $appOptionManager->getAppOption($optionKey, $optionId, $optionParentId);

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
    global $appOptionManager;

    return $appOptionManager->removeAppOption($optionKey,$optionId,$optionValue,$optionParentId);
}

function sl_edit_options(string $key = null, string $old_data = null, string $new_data = null): bool
{
    global $appOptionManager;

    return $appOptionManager->editAppOption($key,$old_data,$new_data);
}

function sl_find_option($search, string $key = null, array $jsonKeys = null){
    global $appOptionManager;

    return $appOptionManager->findAppOption($search, $key, $jsonKeys);
}