<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Headline;
use GuzzleHttp\Client;

class MainController extends AbstractController
{
    public function index(Request $request)
    {
        // creates a task and gives it some dummy data for this example
        $headline = new Headline();

        $form = $this->createFormBuilder($headline)
            ->add('host', TextType::class)
            ->add('title', TextType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Headline'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $headlineData = $form->getData();
            $slugResponse = $this->getSlugResponse($headlineData);

        } else {
            $headlineData = [];
            $slugResponse = "";
        }

        return $this->render('headline/new.html.twig', [
            'form' => $form->createView(),
            'headline' => $headlineData,
            'slugResponse' => json_decode( $slugResponse, true),
            'node' => getenv('K8S_NODE'),
            'host' => gethostname()
        ]);

    }

    private function getSlugResponse(Headline $headline): string
    {
        $server = getenv("SLUGIFY_URI");
        $client = new Client(['base_uri' => $server]);
        $response = $client->request('POST', '/', [
            'form_params' => [
                'subject' => $headline->getTitle(),
                ]
            ]);
        return $response->getBody()->getContents();

    }
}