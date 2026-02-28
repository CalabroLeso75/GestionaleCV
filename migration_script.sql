-- 1. Create employees table
CREATE TABLE IF NOT EXISTS `employees` (
    `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` bigint UNSIGNED DEFAULT NULL,
    `first_name` varchar(255) NOT NULL,
    `last_name` varchar(255) NOT NULL,
    `tax_code` varchar(255) NOT NULL,
    `birth_date` date NOT NULL,
    `birth_place` varchar(255) DEFAULT NULL,
    `gender` enum('male', 'female') DEFAULT NULL,
    `badge_number` varchar(255) DEFAULT NULL,
    `position` varchar(255) DEFAULT NULL,
    `employee_type` enum('internal', 'external') NOT NULL DEFAULT 'internal',
    `status` enum('active', 'terminated', 'suspended', 'pending') NOT NULL DEFAULT 'active',
    `email` varchar(255) DEFAULT NULL,
    `personal_email` varchar(255) DEFAULT NULL,
    `phone` varchar(255) DEFAULT NULL,
    `personal_phone` varchar(255) DEFAULT NULL,
    `is_aib_qualified` tinyint(1) NOT NULL DEFAULT '0',
    `is_emergency_available` tinyint(1) NOT NULL DEFAULT '0',
    `operational_roles` json DEFAULT NULL,
    `organization_id` bigint UNSIGNED DEFAULT NULL,
    `contract_id` bigint UNSIGNED DEFAULT NULL,
    `level_id` bigint UNSIGNED DEFAULT NULL,
    `location_id` bigint UNSIGNED DEFAULT NULL,
    `notes` text,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `employees_tax_code_unique` (`tax_code`),
    UNIQUE KEY `employees_badge_number_unique` (`badge_number`),
    UNIQUE KEY `employees_email_unique` (`email`),
    KEY `employees_user_id_foreign` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
-- 2. Populate employees from anag_persone (Import Logic)
INSERT INTO employees (
        first_name,
        last_name,
        tax_code,
        badge_number,
        birth_date,
        birth_place,
        gender,
        position,
        employee_type,
        status,
        email,
        personal_email,
        phone,
        personal_phone,
        is_aib_qualified,
        is_emergency_available,
        operational_roles,
        organization_id,
        contract_id,
        level_id,
        location_id,
        notes,
        created_at,
        updated_at
    )
SELECT nome,
    cognome,
    codice_fiscale,
    matricola,
    data_nascita,
    luogo_nascita_testo,
    CASE
        WHEN genere = 'uomo' THEN 'male'
        WHEN genere = 'donna' THEN 'female'
        ELSE NULL
    END,
    ccnl_posizione,
    CASE
        WHEN tipo_personale = 'interno' THEN 'internal'
        WHEN tipo_personale = 'esterno' THEN 'external'
        ELSE 'internal'
    END,
    CASE
        WHEN stato_rapporto = 'operativo' THEN 'active'
        WHEN stato_rapporto = 'cessato' THEN 'terminated'
        WHEN stato_rapporto = 'sospeso' THEN 'suspended'
        WHEN stato_rapporto = 'in_attesa' THEN 'pending'
        ELSE 'active'
    END,
    email_aziendale,
    email_personale,
    telefono_aziendale,
    telefono_personale,
    requisiti_aib,
    disponibile_emergenze,
    ruoli_operativi,
    NULL,
    -- organization_id
    NULL,
    -- contract_id
    NULL,
    -- level_id
    NULL,
    -- location_id
    note,
    NOW(),
    NOW()
FROM anag_persone
WHERE NOT EXISTS (
        SELECT 1
        FROM employees
        WHERE employees.tax_code = anag_persone.codice_fiscale
    );