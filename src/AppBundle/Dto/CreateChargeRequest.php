<?php

namespace AppBundle\Dto;

use AppBundle\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;

class CreateChargeRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Choice({"MEDICARE", "MEDICAID", "COMMERCIAL"})
     */
    public $payerType;

    /**
     * @Assert\NotBlank
     * @Assert\Positive
     */
    public $chargeAmountCents;

    /**
     * @Assert\NotBlank
     * @Assert\Date
     */
    public $serviceDate;

    /**
     * @Assert\NotBlank
     * @Assert\Type("int")
     */
    public $patient;

    /**
     * @Assert\NotBlank
     * @Assert\Type("array")
     * @Assert\Count(min=1, minMessage="At least one procedure code is required.")
     * @Assert\All({
     *     @Assert\NotBlank,
     *     @Assert\Type("string")
     * })
     */
    public $procedureCodes = [];

    /**
     * @Assert\Type("array")
     * @Assert\All({
     *     @AppAssert\Icd10
     * })
     */
    public $diagnosisCodes = [];
}
