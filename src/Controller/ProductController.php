<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ImageUploader;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/product')]
class ProductController extends AbstractController
{
    #[Route('/ajax-product-dt')]
    public function AjaxdtAction(ProductRepository $productRepository): JsonResponse
    {
        
        $product = $productRepository->findAll();

        if($product == null) {
            $response = new Response('Pas de produits disponibles', Response::HTTP_OK);
            return $response;
        }
        else {


        foreach($product as $item) {

           
             $results[] = array(
                'id'  => $item->getId(), 
                'name' => $item->getName(),
                'description' => $item->getDescription(),
                'price' => $item->getPrice(),
                'active' => $item->isActive(),
                'quantity' => $item->getQuantity(),
                'Created At' => $item->getCreatedAt(),
                'Updated At' => $item->getUpdatedAt()
                
                  
             );
        }

        return new JsonResponse($results);
        }
    
    }


    
    #[Route('/ajax-product')]
    public function AjaxAction(Request $request, ProductRepository $productRepository)
    {
        
        $args = $request->query->get('q');
        $product = $productRepository->Searchproduct($args);

        if($args == null) {
            $response = new Response('Pas de Produit avec ce nom', Response::HTTP_OK);
            return $response;
        }
        else {

            foreach($product as $item) {
        
                $results[] = array(
                   'id'  => $item->getId(), 
                   'name' => $item->getName()
                     
                );
               }

            
        return new JsonResponse($results);
}
    
    }
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('product/index.html.twig');
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository, ImageUploader $ImageUploader, SluggerInterface $slugger): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()) {
            
            $product->setCreatedAt(new \DateTimeImmutable);

                        /** @var UploadedFile $ProductImageFile */
                        $ImagePFile = $form->get('image')->getData();

                        // this condition is needed because the 'image' field is not required
                        // so the image file must be processed only when a file is uploaded
                        if ($ImagePFile) {
                            $originalFilename = pathinfo($ImagePFile->getClientOriginalName(), PATHINFO_FILENAME);
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $slugger->slug($originalFilename);
                            $newFilename = $safeFilename.'-'.uniqid().'.'.$ImagePFile->guessExtension();
            
                            // Move the file to the directory where ImagePs are stored
                                $ImagePFile->move(
                                    $this->getParameter('upload_dir'),
                                    $newFilename
                                );

            
                            // updates the 'ImageFilename' property to store the PDF file name
                            // instead of its contents
                            $product->setImage($newFilename);
                        }
            
            $productRepository->add($product, true);
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpdatedAt(new \DateTimeImmutable);
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
