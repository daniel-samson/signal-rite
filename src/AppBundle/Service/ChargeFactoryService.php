<?php

namespace AppBundle\Service;

use AppBundle\Dto\CreateChargeRequest;
use AppBundle\Entity\Charge;
use AppBundle\Entity\ProcedureCode;
use AppBundle\Entity\Diagnosis;
use AppBundle\Enums\ProcedureCodeEnum;
use AppBundle\Repository\PatientRepository;
use AppBundle\Repository\ProcedureCodeRepository;
use AppBundle\Repository\DiagnosisRepository;
use Doctrine\ORM\EntityManagerInterface;

class ChargeFactoryService
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ProcedureCodeRepository */
    private $procedureCodeRepository;

    /** @var DiagnosisRepository */
    private $diagnosisRepository;

    public function __construct(
        EntityManagerInterface  $entityManager,
        ProcedureCodeRepository $procedureCodeRepository,
        DiagnosisRepository     $diagnosisRepository,
        PatientRepository       $patientRepository
    ) {
        $this->entityManager = $entityManager;
        $this->procedureCodeRepository = $procedureCodeRepository;
        $this->diagnosisRepository = $diagnosisRepository;
        $this->patientRepository = $patientRepository;
    }

    /**
     * @throws \Exception
     */
    public function createFromRequest(CreateChargeRequest $dto): Charge
    {
        $charge = new Charge();
        $charge->setPayerType($dto->payerType);
        $charge->setChargeAmountCents($dto->chargeAmountCents);
        $charge->setServiceDate(new \DateTimeImmutable($dto->serviceDate));
        $charge->setPatient($this->patientRepository->find($dto->patient));

        // Add procedure codes
        foreach ($dto->procedureCodes as $code) {
            $code = ProcedureCodeEnum::normalize($code);
            $procedureCode = $this->findOrCreateProcedureCode($code);
            $charge->addProcedureCode($procedureCode);
        }

        // Add diagnosis codes
        foreach ($dto->diagnosisCodes as $code) {
            $diagnosis = $this->findOrCreateDiagnosis($code);
            $charge->addDiagnosis($diagnosis);
        }

        return $charge;
    }

    private function findOrCreateProcedureCode(string $code): ProcedureCode
    {
        $procedureCode = $this->procedureCodeRepository->findByCode($code);

        if ($procedureCode === null) {
            $procedureCode = new ProcedureCode();
            $procedureCode->setCode($code);
            $procedureCode->setDescription(''); // To be filled later or via import
            $this->entityManager->persist($procedureCode);
        }

        return $procedureCode;
    }

    private function findOrCreateDiagnosis(string $code): Diagnosis
    {
        $diagnosis = $this->diagnosisRepository->findOneBy(['code' => strtoupper(trim($code))]);

        if ($diagnosis === null) {
            $diagnosis = new Diagnosis();
            $diagnosis->setCode($code);
            $diagnosis->setDescription(''); // To be filled later or via import
            $this->entityManager->persist($diagnosis);
        }

        return $diagnosis;
    }
}
