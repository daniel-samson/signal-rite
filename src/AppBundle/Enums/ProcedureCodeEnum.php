<?php

declare(strict_types=1);

namespace AppBundle\Enums;

use AppBundle\Enums\Traits\NormalizesValuesTrait;
use PhpCompatible\Enum\Enum;
use PhpCompatible\Enum\Value;

/**
 * CPT and HCPCS procedure codes.
 *
 * @method static Value cptOfficeVisitLevel1()
 * @method static Value cptOfficeVisitLevel2()
 * @method static Value cptOfficeVisitLevel3()
 * @method static Value cptOfficeVisitLevel4()
 * @method static Value cptOfficeVisitLevel5()
 * @method static Value cptEstablishedVisitLevel1()
 * @method static Value cptEstablishedVisitLevel2()
 * @method static Value cptEstablishedVisitLevel3()
 * @method static Value cptEstablishedVisitLevel4()
 * @method static Value cptEstablishedVisitLevel5()
 * @method static Value cptChestXray()
 * @method static Value cptMriLumbarSpine()
 * @method static Value cptShoulderXray()
 * @method static Value cptCtAbdomenPelvis()
 * @method static Value cptEcgComplete()
 * @method static Value cptEcgInterpretation()
 * @method static Value cptEchocardiogram()
 * @method static Value cptFineNeedleAspiration()
 * @method static Value cptBreastBiopsy()
 * @method static Value cptLaparoscopicCholecystectomy()
 * @method static Value cptBasicMetabolicPanel()
 * @method static Value cptComprehensiveMetabolicPanel()
 * @method static Value cptCompleteBloodCount()
 * @method static Value hcpcsAmbulanceService()
 * @method static Value hcpcsCrutches()
 * @method static Value hcpcsWristOrthosis()
 * @method static Value hcpcsDexamethasoneInjection()
 * @method static Value hcpcsOndansetronInjection()
 * @method static Value hcpcsUnclassifiedDrug()
 * @method static Value modifierSignificantSeparateEM()
 * @method static Value modifierProfessionalComponent()
 * @method static Value modifierDistinctProceduralService()
 * @method static Value modifierTelehealth()
 */
final class ProcedureCodeEnum extends Enum
{
    use NormalizesValuesTrait;

    /*
     * =========================
     * CPT – Evaluation & Management
     * =========================
     */
    protected $cptOfficeVisitLevel1 = '99201';
    protected $cptOfficeVisitLevel2 = '99202';
    protected $cptOfficeVisitLevel3 = '99203';
    protected $cptOfficeVisitLevel4 = '99204';
    protected $cptOfficeVisitLevel5 = '99205';

    protected $cptEstablishedVisitLevel1 = '99211';
    protected $cptEstablishedVisitLevel2 = '99212';
    protected $cptEstablishedVisitLevel3 = '99213';
    protected $cptEstablishedVisitLevel4 = '99214';
    protected $cptEstablishedVisitLevel5 = '99215';

    /*
     * =========================
     * CPT – Radiology
     * =========================
     */
    protected $cptChestXray = '71020';
    protected $cptMriLumbarSpine = '72148';
    protected $cptShoulderXray = '73030';
    protected $cptCtAbdomenPelvis = '74177';

    /*
     * =========================
     * CPT – Cardiology
     * =========================
     */
    protected $cptEcgComplete = '93000';
    protected $cptEcgInterpretation = '93010';
    protected $cptEchocardiogram = '93306';

    /*
     * =========================
     * CPT – Surgery
     * =========================
     */
    protected $cptFineNeedleAspiration = '10021';
    protected $cptBreastBiopsy = '19120';
    protected $cptLaparoscopicCholecystectomy = '47562';

    /*
     * =========================
     * CPT – Pathology / Lab
     * =========================
     */
    protected $cptBasicMetabolicPanel = '80048';
    protected $cptComprehensiveMetabolicPanel = '80053';
    protected $cptCompleteBloodCount = '85025';

    /*
     * =========================
     * HCPCS Level II – Supplies / DME
     * =========================
     */
    protected $hcpcsAmbulanceService = 'A0428';
    protected $hcpcsCrutches = 'E0114';
    protected $hcpcsWristOrthosis = 'L3908';

    /*
     * =========================
     * HCPCS Level II – Drugs
     * =========================
     */
    protected $hcpcsDexamethasoneInjection = 'J1100';
    protected $hcpcsOndansetronInjection = 'J2405';
    protected $hcpcsUnclassifiedDrug = 'J3490';

    /*
     * =========================
     * Common Modifiers
     * =========================
     */
    protected $modifierSignificantSeparateEM = '25';
    protected $modifierProfessionalComponent = '26';
    protected $modifierDistinctProceduralService = '59';
    protected $modifierTelehealth = 'GT';

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
        $code = self::normalize($code);
        return preg_match(
                '/^(\d{5}|[A-Z]\d{4})(-[A-Z0-9]{2})*$/',
                $code
            ) === 1;
    }

    public static function normalize(string $code): string
    {
        return strtoupper(trim($code));
    }
}
