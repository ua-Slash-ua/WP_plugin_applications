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

function get_applications($dataFilter): array {
    global $appManager, $appMetaManager;

    $filters = $dataFilter['value'] ?? [];

    $applications = [];
    $allApplications = $appManager->get_all();

    foreach ($allApplications as $application) {
        $idApp = $application->id;
        $match = true;

        // === Фільтр за типом заявки ===
        if (isset($filters['type']) && $filters['type'] !== '' && $application->type !== $filters['type']) {
            $match = false;
        }

        // === Фільтр за статусом перегляду ===
        if (isset($filters['viewed']) && $filters['viewed'] !== '' && (string)$application->viewed !== (string)$filters['viewed']) {
            $match = false;
        }

        // === Фільтр за датою створення (від) ===
        if (isset($filters['date_start']) && $filters['date_start'] !== '' && strtotime($application->created_at) < strtotime($filters['date_start'])) {
            $match = false;
        }

        // === Фільтр за конкретним полем заявки ===
        if (
            isset($filters['label_name'], $filters['label_value']) &&
            $filters['label_name'] !== '' &&
            $filters['label_value'] !== ''
        ) {
            $metaMatches = $appMetaManager->getByFields($idApp, $filters['label_name'], $filters['label_value']);
            if (empty($metaMatches)) {
                $match = false;
            }
        }

        // === Якщо всі умови виконано — додаємо ===
        if ($match) {
            $labels = $appMetaManager->getAll($idApp);
            $applicationArray = (array) $application;
            $applicationArray['labels'] = $labels;
            $applications[] = $applicationArray;
        }
    }

    return $applications;
}



function set_view(int $applicationId, bool $viewed): bool
{
    global $appManager;
    $viewed = $viewed? 1:0;
    return $appManager->setViewedStatus($applicationId, $viewed);
}

function remove_application(int $applicationId): bool
{
    global $appManager, $appMetaManager;
    $statusApp = $appManager->delete($applicationId);
    $statusAppMeta = $appMetaManager->deleteByMainId($applicationId);
    if ($statusApp && $statusAppMeta) {
        return true;
    }else{
        return false;
    }
}
