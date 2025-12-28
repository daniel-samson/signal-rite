<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use ReflectionClass;

final class ChargeProcedureCodeEnum extends ConstantEnum
{
    /*
     * =========================
     * CPT – Evaluation & Management
     * =========================
     */
    public const CPT_OFFICE_VISIT_LEVEL_1 = '99201';
    public const CPT_OFFICE_VISIT_LEVEL_2 = '99202';
    public const CPT_OFFICE_VISIT_LEVEL_3 = '99203';
    public const CPT_OFFICE_VISIT_LEVEL_4 = '99204';
    public const CPT_OFFICE_VISIT_LEVEL_5 = '99205';

    public const CPT_ESTABLISHED_VISIT_LEVEL_1 = '99211';
    public const CPT_ESTABLISHED_VISIT_LEVEL_2 = '99212';
    public const CPT_ESTABLISHED_VISIT_LEVEL_3 = '99213';
    public const CPT_ESTABLISHED_VISIT_LEVEL_4 = '99214';
    public const CPT_ESTABLISHED_VISIT_LEVEL_5 = '99215';

    /*
     * =========================
     * CPT – Radiology
     * =========================
     */
    public const CPT_CHEST_XRAY = '71020';
    public const CPT_MRI_LUMBAR_SPINE = '72148';
    public const CPT_SHOULDER_XRAY = '73030';
    public const CPT_CT_ABDOMEN_PELVIS = '74177';

    /*
     * =========================
     * CPT – Cardiology
     * =========================
     */
    public const CPT_ECG_COMPLETE = '93000';
    public const CPT_ECG_INTERPRETATION = '93010';
    public const CPT_ECHOCARDIOGRAM = '93306';

    /*
     * =========================
     * CPT – Surgery
     * =========================
     */
    public const CPT_FINE_NEEDLE_ASPIRATION = '10021';
    public const CPT_BREAST_BIOPSY = '19120';
    public const CPT_LAPAROSCOPIC_CHOLECYSTECTOMY = '47562';

    /*
     * =========================
     * CPT – Pathology / Lab
     * =========================
     */
    public const CPT_BASIC_METABOLIC_PANEL = '80048';
    public const CPT_COMPREHENSIVE_METABOLIC_PANEL = '80053';
    public const CPT_COMPLETE_BLOOD_COUNT = '85025';

    /*
     * =========================
     * HCPCS Level II – Supplies / DME
     * =========================
     */
    public const HCPCS_AMBULANCE_SERVICE = 'A0428';
    public const HCPCS_CRUTCHES = 'E0114';
    public const HCPCS_WRIST_ORTHOSIS = 'L3908';

    /*
     * =========================
     * HCPCS Level II – Drugs
     * =========================
     */
    public const HCPCS_DEXAMETHASONE_INJECTION = 'J1100';
    public const HCPCS_ONDANSETRON_INJECTION = 'J2405';
    public const HCPCS_UNCLASSIFIED_DRUG = 'J3490';

    /*
     * =========================
     * Common Modifiers
     * =========================
     */
    public const MODIFIER_SIGNIFICANT_SEPARATE_E_M = '25';
    public const MODIFIER_PROFESSIONAL_COMPONENT = '26';
    public const MODIFIER_DISTINCT_PROCEDURAL_SERVICE = '59';
    public const MODIFIER_TELEHEALTH = 'GT';


    /**
     * Check whether a string is a valid CPT or HCPCS-style procedure code.
     *
     * Valid formats:
     *  - CPT:            12345
     *  - HCPCS Level II: A1234
     *  - With modifiers: 12345-25, A1234-GT, 12345-25-59
     */
    public static function is(string $code): bool
    {
        $code = strtoupper(trim($code));

        return preg_match(
                '/^(\d{5}|[A-Z]\d{4})(-[A-Z0-9]{2})*$/',
                $code
            ) === 1;
    }
}
