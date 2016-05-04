<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\StockBundle\Entity\Supplier;
use Flower\StockBundle\Form\Type\SupplierType;
use Doctrine\ORM\QueryBuilder;

/**
 * Supplier controller.
 *
 * @Route("/supplier")
 */
class SupplierController extends Controller
{
    /**
     * Lists all Supplier entities.
     *
     * @Route("/", name="supplier")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerStockBundle:Supplier')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'supplier');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Supplier entity.
     *
     * @Route("/{id}/show", name="supplier_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Supplier $supplier)
    {
        $editForm = $this->createForm(new SupplierType(), $supplier, array(
            'action' => $this->generateUrl('supplier_update', array('id' => $supplier->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($supplier->getId(), 'supplier_delete');

        return array(

        'supplier' => $supplier,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Supplier entity.
     *
     * @Route("/new", name="supplier_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $supplier = new Supplier();
        $form = $this->createForm(new SupplierType(), $supplier);

        return array(
            'supplier' => $supplier,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Supplier entity.
     *
     * @Route("/create", name="supplier_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Supplier:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $supplier = new Supplier();
        $form = $this->createForm(new SupplierType(), $supplier);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($supplier);
            $em->flush();

            return $this->redirect($this->generateUrl('supplier_show', array('id' => $supplier->getId())));
        }

        return array(
            'supplier' => $supplier,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Supplier entity.
     *
     * @Route("/{id}/edit", name="supplier_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Supplier $supplier)
    {
        $editForm = $this->createForm(new SupplierType(), $supplier, array(
            'action' => $this->generateUrl('supplier_update', array('id' => $supplier->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($supplier->getId(), 'supplier_delete');

        return array(
            'supplier' => $supplier,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Supplier entity.
     *
     * @Route("/{id}/update", name="supplier_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerStockBundle:Supplier:edit.html.twig")
     */
    public function updateAction(Supplier $supplier, Request $request)
    {
        $editForm = $this->createForm(new SupplierType(), $supplier, array(
            'action' => $this->generateUrl('supplier_update', array('id' => $supplier->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('supplier_show', array('id' => $supplier->getId())));
        }
        $deleteForm = $this->createDeleteForm($supplier->getId(), 'supplier_delete');

        return array(
            'supplier' => $supplier,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="supplier_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('supplier', $field, $type);

        return $this->redirect($this->generateUrl('supplier'));
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
     * Deletes a Supplier entity.
     *
     * @Route("/{id}/delete", name="supplier_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Supplier $supplier, Request $request)
    {
        $form = $this->createDeleteForm($supplier->getId(), 'supplier_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($supplier);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('supplier'));
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
