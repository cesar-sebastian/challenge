<?php
namespace App\Controller;

use App\Api\ApiProblem;
use App\Entity\ImportProduct;
use App\Message\Import;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ImportProductAction
{

    private ValidatorInterface $validator;
    private MessageBusInterface $messageBus;

    /**
     * ImportProductAction constructor.
     * @param ValidatorInterface $validator
     * @param MessageBusInterface $messageBus
     */
    public function __construct(ValidatorInterface $validator,MessageBusInterface $messageBus)
    {
        $this->validator = $validator;
        $this->messageBus = $messageBus;
    }

    /**
     * @param Request $request
     * @return ImportProduct|Response
     */
    public function __invoke(Request $request): ImportProduct|Response
    {
        /** @var UploadedFile $file */
        $file = $request->files->get('file');

        $errors = $this->validator->validate($file, [
            new NotBlank()
        ]);

        if (count($errors) > 0) {
            return new Response(json_encode($errors), 500);
        }

        $rawFile = file_get_contents($file->getPathname());
        $data = json_decode($rawFile, true);

        if ($data === null) {
            return new Response('Invalid Json', 500);
        }

        $message = new Import();
        $message->setData($rawFile);

        $this->messageBus->dispatch($message);

        return new Response('Processing', 201);
    }

}
