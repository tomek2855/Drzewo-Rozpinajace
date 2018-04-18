<?php
/**
 * Created by PhpStorm.
 * User: tomcio
 * Date: 18.04.18
 * Time: 11:36
 */

namespace App\Controller;

use App\Entity\Category;
use App\Utils\DepthOperations;
use App\Utils\UserOperations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AdminController extends Controller {

    public function __construct(UserOperations $user) {
        if(!$user->isLogin()) {
            $response = new Response('<script>window.location.replace("/");</script>', Response::HTTP_UNAUTHORIZED);
            $response->send();
            die;
        }
    }

    /**
     * @Route("category/add/{id}",
     *     name="category-add",
     *     requirements={
     *         "id"="\d+"
     *     }
     * )
     */
    public function addCategory($id, ValidatorInterface $validator){
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $parent = $em->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        $maxSequence = $em->getRepository(Category::class)->getMaxSequenceInParent($parent);

        $leaf = new Category();
        $leaf->setName($request->request->get('name'));
        $leaf->setParent($parent);
        $leaf->setDepth($parent->getDepth()+1);
        $leaf->setSequence($maxSequence + 1);

        $errors = $validator->validate($leaf);
        if(count($errors)){
            return new Response('Błędny formularz!', Response::HTTP_NOT_ACCEPTABLE);
        }

        $em->persist($leaf);
        $em->flush();

        return $this->redirectToRoute('category-show-depth', ['id' => $id]);
    }

    /**
     * @Route("category/move",
     *     name="category-move",
     *     methods={"POST"}
     *  )
     */
    public function moveCategory(){
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();
        $depth = new DepthOperations();

        $leafId = $request->request->get('leaf_id');
        $newParentId = $request->request->get('new_parent_id');


        $leaf = $em->getRepository(Category::class)->findOneBy([
            'id' => $leafId
        ]);


        $newParent = $em->getRepository(Category::class)->findOneBy([
            'id' => $newParentId
        ]);

        $leaf->setParent($newParent);
        $leaf->setDepth($newParent->getDepth() + 1);
        $leaf->setSequence($em->getRepository(Category::class)->getMaxSequenceInParent($newParent->getId()) + 1);
        $depth->recalculateDepth($leaf);

        $em->persist($leaf);
        $em->flush();

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route("category/delete-category/{id}",
     *     name="category-delete-category",
     *     requirements={
     *          "id"="\d+"
     *      },
     *     methods={"DELETE"}
     * )
     */
    public function deleteCategory($id){
        $em = $this->getDoctrine()->getManager();
        $depth = new DepthOperations();

        $leaf = $em->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        $leafChildren = $leaf->getChildren();

        foreach ($leafChildren as $child){
            $child->setParent($leaf->getParent());
            $child->setDepth($leaf->getDepth());
            $depth->recalculateDepth($leaf);
        }

        $em->remove($leaf);
        $em->flush();

        return new Response('OK', Response::HTTP_OK);
    }


    /**
     * @Route("category/delete-branch/{id}",
     *     name="category-delete-branch",
     *     requirements={
     *          "id"="\d+"
     *      },
     *     methods={"DELETE"}
     * )
     */
    public function deleteBranch($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        $em->remove($leaf);
        $em->flush();

        return new Response('OK', Response::HTTP_OK);
    }

    /**
     * @Route("category/edit/{id}",
     *     name="category-edit",
     *     requirements={
     *          "id"="\d+"
     *     },
     *     methods={"POST"}
     * )
     */
    public function editCategory($id, ValidatorInterface $validator){
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Category::class)->findOneBy([
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

        return $this->redirectToRoute('category-show-depth', ['id' => $id]);
    }

    /**
     * @Route("category/show-categories/{id}",
     *     name="category-show-depth",
     *     requirements={
     *          "id"="\d+"
     *     }
     *  )
     */
    public function categoryShowAllCategories($id = 1){
        $tree = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'id' => 1
        ]);

        $categories = $this->getDoctrine()->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        return $this->render('category/index.html.twig', [
            'tree' => $tree,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("category/set-depth/{id}/up",
     *     name="category-set-depth-up",
     *     requirements={
     *          "id"="\d+"
     *     }
     *  )
     */
    public function depthUp($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        $parentId = $leaf->getParent()->getId();
        $sequence = $leaf->getSequence();

        do {
            $leaf2 = $em->getRepository(Category::class)->findOneBy([
                'parent' => $parentId,
                'sequence' => $sequence - 1
            ]);

            $sequence--;
        } while($leaf2 == null);

        $temp = $leaf->getSequence();
        $leaf->setSequence($leaf2->getSequence());
        $leaf2->setSequence($temp);

        $em->flush();

        return $this->redirectToRoute('category-show-depth', ['id' => $parentId]);
    }

    /**
     * @Route("category/set-depth/{id}/down",
     *     name="category-set-depth-down",
     *     requirements={
     *          "id"="\d+",
     *     }
     *  )
     */
    public function depthDown($id){
        $em = $this->getDoctrine()->getManager();

        $leaf = $em->getRepository(Category::class)->findOneBy([
            'id' => $id
        ]);

        $parentId = $leaf->getParent()->getId();
        $sequence = $leaf->getSequence();

        do {
            $leaf2 = $em->getRepository(Category::class)->findOneBy([
                'parent' => $parentId,
                'sequence' => $sequence + 1
            ]);

            $sequence++;
        } while($leaf2 == null);


        $temp = $leaf->getSequence();
        $leaf->setSequence($leaf2->getSequence());
        $leaf2->setSequence($temp);

        $em->flush();

        return $this->redirectToRoute('category-show-depth', ['id' => $parentId]);
    }

}