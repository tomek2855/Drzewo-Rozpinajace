<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller {

    /**
     * @Route("/", name="category")
     */
    public function index($id = 1) {
        $tree = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        return $this->render('category/categories.html.twig', [
           'categories' => $tree
        ]);
    }

    /**
     * @Route("/category/{id}",
     *     name="category-show",
     *     requirements={
     *         "id"="\d+"
     *     }
     * )
     */
    public function showCategory($id){
        return $this->index($id);
    }

}