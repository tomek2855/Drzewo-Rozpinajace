<?php

namespace App\Controller;

use App\Entity\Tree;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TreeController extends Controller {
    /**
     * @Route("/tree", name="tree")
     */
    public function index($id = 1) {
        $tree = $this->getDoctrine()->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        return $this->render('tree/index.html.twig', [
           'tree' => $tree
        ]);
    }

    /**
     * @Route("/tree/{id}",
     *     name="tree-show",
     *     requirements={
     *         "id"="\d+"
     *           }
     * )
     */
    public function showLeaf($id){
        return $this->index($id);
    }

    /**
     * @Route("tree/add",
     *     name="tree-add"
     * )
     */
    public function addLeaf(){

    }

    /**
     * @Route("/tree/add", name="tree-add")
     */
    public function add(){
        $em = $this->getDoctrine()->getManager();

        $root = $em->getRepository(Tree::class)->findOneBy([
            'id' => 9
        ]);

        $tree = new Tree();
        $tree->setName('Mega');
        $tree->setParent($root);

        $em->persist($tree);
        $em->flush();

        return new Response('Dodano do bazy!');
    }
}