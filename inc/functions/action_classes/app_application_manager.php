<?php

use sl_app\ApplicationManager;
use sl_app\ApplicationMetaManager;
$appManager = new ApplicationManager();
$appMetaManager = new ApplicationMetaManager();
function add_application(string $name, string $type, array $labels): string
{
    global $appManager, $appMetaManager;
    if (!$name || !$type || !$labels) {
        return 'Не передані поля';
    }

    // Перетворюємо labels в асоціативний масив, якщо потрібно
    if (array_keys($labels) === range(0, count($labels) - 1)) {
        // Масив із кількома об'єктами [{email: "..."}]
        $prepared_labels = $labels;
    } else {
        // Один об'єкт {email: "..."} → обгортаємо в масив
        $prepared_labels = [$labels];
    }
    $idApp = $appManager->add($name, $type);
    foreach ($prepared_labels as $prepared_label) {
        foreach ($prepared_label as $label_key => $label_value) {
            if (!is_string($label_key) || !is_string($label_value)) {
                return 'Неправильний формат полів';
            }
            $status = $appMetaManager->add($idApp, $label_key, $label_value);
            if ($status){
                error_log('Поле'. $label_key. 'додано');
            }else{
                error_log('Поле'. $label_key. 'NOT додано');
            }

        }
    }

    return '';

}

function get_applications(): array
{
    global $appManager, $appMetaManager;
    $applications = [];
    $allApplications = $appManager->get_all();

    foreach ($allApplications as $application) {
        $idApp = $application->id;

        // Отримуємо мета-дані
        $labels = $appMetaManager->getAll($idApp);

        // Перетворюємо об'єкт на масив (щоб можна було додати ключ)
        $applicationArray = (array) $application;
        $applicationArray['labels'] = $labels;

        $applications[] = $applicationArray;
    }

    return $applications;
}

