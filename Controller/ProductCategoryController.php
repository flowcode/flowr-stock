<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\ProductCategory;
use Flower\StockBundle\Form\Type\ProductCategoryType;
use Doctrine\ORM\QueryBuilder;

/**
 * ProductCategory controller.
 *
 * @Route("/productcategory")
 */
class ProductCategoryController extends Controller
{
    /**
     * Lists all ProductCategory entities.
     *
     * @Route("/", name="productcategory")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\ProductCategory')->createQueryBuilder('p');
        $this->addQueryBuilderSort($qb, 'productcategory');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a ProductCategory entity.
     *
     * @Route("/{id}/show", name="productcategory_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(ProductCategory $productcategory)
    {
        $editForm = $this->createForm(new ProductCategoryType(), $productcategory, array(
            'action' => $this->generateUrl('productcategory_update', array('id' => $productcategory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productcategory->getId(), 'productcategory_delete');

        return array(

        'productcategory' => $productcategory,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new ProductCategory entity.
     *
     * @Route("/new", name="productcategory_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $productcategory = new ProductCategory();
        $form = $this->createForm(new ProductCategoryType(), $productcategory);

        return array(
            'productcategory' => $productcategory,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new ProductCategory entity.
     *
     * @Route("/create", name="productcategory_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:ProductCategory:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $productcategory = new ProductCategory();
        $form = $this->createForm(new ProductCategoryType(), $productcategory);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($productcategory);
            $em->flush();

            return $this->redirect($this->generateUrl('productcategory_show', array('id' => $productcategory->getId())));
        }

        return array(
            'productcategory' => $productcategory,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ProductCategory entity.
     *
     * @Route("/{id}/edit", name="productcategory_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(ProductCategory $productcategory)
    {
        $editForm = $this->createForm(new ProductCategoryType(), $productcategory, array(
            'action' => $this->generateUrl('productcategory_update', array('id' => $productcategory->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($productcategory->getId(), 'productcategory_delete');

        return array(
            'productcategory' => $productcategory,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing ProductCategory entity.
     *
     * @Route("/{id}/update", name="productcategory_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:ProductCategory:edit.html.twig")
     */
    public function updateAction(ProductCategory $productcategory, Request $request)
    {
        $editForm = $this->createForm(new ProductCategoryType(), $productcategory, array(
            'action' => $this->generateUrl('productcategory_update', array('id' => $productcategory->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('productcategory_show', array('id' => $productcategory->getId())));
        }
        $deleteForm = $this->createDeleteForm($productcategory->getId(), 'productcategory_delete');

        return array(
            'productcategory' => $productcategory,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="productcategory_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('productcategory', $field, $type);

        return $this->redirect($this->generateUrl('productcategory'));
    }

    /**
     * @param string $name  session name
     * @param string $field field name
     * @param string $type  sort type ("ASC"/"DESC")
     */
    protected function setOrder($name, $field, $type = 'ASC')
    {
        $this->getRequest()->getSession()->set('sort.' . $name, array('field' => $field, 'type' => $type));
    }

    /**
     * @param  string $name
     * @return array
     */
    protected function getOrder($name)
    {
        $session = $this->getRequest()->getSession();

        return $session->has('sort.' . $name) ? $session->get('sort.' . $name) : null;
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $name
     */
    protected function addQueryBuilderSort(QueryBuilder $qb, $name)
    {
        $alias = current($qb->getDQLPart('from'))->getAlias();
        if (is_array($order = $this->getOrder($name))) {
            $qb->orderBy($alias . '.' . $order['field'], $order['type']);
        }
    }

    /**
     * Deletes a ProductCategory entity.
     *
     * @Route("/{id}/delete", name="productcategory_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(ProductCategory $productcategory, Request $request)
    {
        $form = $this->createDeleteForm($productcategory->getId(), 'productcategory_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($productcategory);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('productcategory'));
    }

    /**
     * Create Delete form
     *
     * @param integer                       $id
     * @param string                        $route
     * @return \Symfony\Component\Form\Form
     */
    protected function createDeleteForm($id, $route)
    {
        return $this->createFormBuilder(null, array('attr' => array('id' => 'delete')))
            ->setAction($this->generateUrl($route, array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

}
