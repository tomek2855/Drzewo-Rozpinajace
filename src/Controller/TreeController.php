<?php

namespace App\Controller;

use App\Entity\Tree;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

    private function recalculateDepth($p){
        if($p->getChildren())
            foreach ($p->getChildren() as $child){
                    $child->setDepth($p->getDepth()+1);
                    $this->recalculateDepth($child);
                }

        return true;
    }

    /**
     * @Route("/tree/{id}",
     *     name="tree-show",
     *     requirements={
     *         "id"="\d+"
     *     }
     * )
     */
    public function showLeaf($id){
        return $this->index($id);
    }


    /**
     * @Route("tree/add/{id}",
     *     name="tree-add",
     *     requirements={
     *         "id"="\d+"
     *     }
     * )
     */
    public function addLeaf($id, ValidatorInterface $validator){
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $leaf = new Tree();
        $leaf->setName($request->request->get('name'));
        $leaf->setParent($parent);
        $leaf->setDepth($parent->getDepth()+1);
        $leaf->setSequence($em->getRepository(Tree::class)->getMaxSequenceInParent($parent) + 1);

        $errors = $validator->validate($leaf);
        if(count($errors)){
            return new Response('Błędny formularz!', Response::HTTP_NOT_ACCEPTABLE);
        }

        $em->persist($leaf);
        $em->flush();

        return $this->redirectToRoute('tree');
    }

    /**
     * @Route("tree/move",
     *     name="tree-move",
     *     methods={"POST"}
     *  )
     */
    public function moveLeaf(){
        $request = Request::createFromGlobals();

        $leafId = $request->request->get('leaf_id');
        $newParentId = $request->request->get('new_parent_id');

        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $leafId
        ]);


        $newParent = $em->getRepository(Tree::class)->findOneBy([
            'id' => $newParentId
        ]);

        $leaf->setParent($newParent);
        $leaf->setDepth($newParent->getDepth() + 1);
        $leaf->setSequence($em->getRepository(Tree::class)->getMaxSequenceInParent($newParent->getId()) + 1);
        $this->recalculateDepth($leaf);

        $em->persist($leaf);
        $em->flush();

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route("tree/delete-leaf/{id}",
     *     name="tree-delete-leaf",
     *     requirements={
     *          "id"="\d+"
     *      }
     * )
     */
    public function deleteLeaf($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $leafChildren = $leaf->getChildren();

        foreach ($leafChildren as $child){
            $child->setParent($leaf->getParent());
            $child->setDepth($leaf->getDepth());
            $this->recalculateDepth($child);
        }

        $em->remove($leaf);
        $em->flush();

        return $this->redirectToRoute('tree');
    }


    /**
     * @Route("tree/delete-branch/{id}",
     *     name="tree-delete-branch",
     *     requirements={
     *          "id"="\d+"
     *      }
     * )
     */
    public function deleteBranch($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $em->remove($leaf);
        $em->flush();

        return $this->redirectToRoute('tree');
    }

    /**
     * @Route("tree/edit/{id}",
     *     name="tree-edit",
     *     requirements={
     *          "id"="\d+"
     *     },
     *     methods={"POST"}
     * )
     */
    public function treeEdit($id, ValidatorInterface $validator){
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $newName = $request->request->get('new_name');

        $leaf->setName($newName);

        $errors = $validator->validate($leaf);
        if(count($errors)){
            return new Response('Błędny formularz!', Response::HTTP_NOT_ACCEPTABLE);
        }

        $em->persist($leaf);
        $em->flush();

        return $this->redirectToRoute('tree');
    }

    /**
     * @Route("tree/show-children/{id}",
     *     name="tree-show-depth",
     *     requirements={
     *          "id"="\d+"
     *     }
     *  )
     */
    public function treeShowOneDepth($id = 1){
        $tree = $this->getDoctrine()->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);


        return $this->render('tree/show-children.twig', [
            'children' => $tree->getChildren()
        ]);
    }

    /**
     * @Route("tree/set-depth/{id}/up",
     *     name="tree-set-depth-up",
     *     requirements={
     *          "id"="\d+"
     *     }
     *  )
     */
    public function depthUp($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $parentId = $leaf->getParent()->getId();

        $leaf2 = $em->getRepository(Tree::class)->findOneBy([
            'parent' => $parentId,
            'sequence' => $leaf->getSequence()-1
        ]);

        $temp = $leaf->getSequence();
        $leaf->setSequence($leaf2->getSequence());
        $leaf2->setSequence($temp);

        $em->flush();

        return $this->redirectToRoute('tree-show-depth', ['id' => $parentId]);
    }

    /**
     * @Route("tree/set-depth/{id}/down",
     *     name="tree-set-depth-down",
     *     requirements={
     *          "id"="\d+",
     *     }
     *  )
     */
    public function depthDown($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Tree::class)->findOneBy([
            'id' => $id
        ]);

        $parentId = $leaf->getParent()->getId();

        $leaf2 = $em->getRepository(Tree::class)->findOneBy([
            'parent' => $parentId,
            'sequence' => $leaf->getSequence()+1
        ]);

        $temp = $leaf->getSequence();
        $leaf->setSequence($leaf2->getSequence());
        $leaf2->setSequence($temp);

        $em->flush();

        return $this->redirectToRoute('tree-show-depth', ['id' => $parentId]);
    }

}