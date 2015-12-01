<?php

namespace Flower\StockBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Flower\ModelBundle\Entity\Stock\Service;
use Flower\StockBundle\Form\Type\ServiceType;
use Doctrine\ORM\QueryBuilder;

/**
 * Service controller.
 *
 * @Route("/service")
 */
class ServiceController extends Controller
{
    /**
     * Lists all Service entities.
     *
     * @Route("/", name="service")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository('FlowerModelBundle:Stock\Service')->createQueryBuilder('s');
        $this->addQueryBuilderSort($qb, 'service');
        $paginator = $this->get('knp_paginator')->paginate($qb, $request->query->get('page', 1), 20);
        
        return array(
            'paginator' => $paginator,
        );
    }

    /**
     * Finds and displays a Service entity.
     *
     * @Route("/{id}/show", name="service_show", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function showAction(Service $service)
    {
        $editForm = $this->createForm(new ServiceType(), $service, array(
            'action' => $this->generateUrl('service_update', array('id' => $service->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($service->getId(), 'service_delete');

        return array(

        'service' => $service,
        'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),

        );
    }

    /**
     * Displays a form to create a new Service entity.
     *
     * @Route("/new", name="service_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $service = new Service();
        $form = $this->createForm(new ServiceType(), $service);

        return array(
            'service' => $service,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Service entity.
     *
     * @Route("/create", name="service_create")
     * @Method("POST")
     * @Template("FlowerStockBundle:Service:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $service = new Service();
        $form = $this->createForm(new ServiceType(), $service);
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($service);
            $em->flush();

            return $this->redirect($this->generateUrl('service_show', array('id' => $service->getId())));
        }

        return array(
            'service' => $service,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Service entity.
     *
     * @Route("/{id}/edit", name="service_edit", requirements={"id"="\d+"})
     * @Method("GET")
     * @Template()
     */
    public function editAction(Service $service)
    {
        $editForm = $this->createForm(new ServiceType(), $service, array(
            'action' => $this->generateUrl('service_update', array('id' => $service->getid())),
            'method' => 'PUT',
        ));
        $deleteForm = $this->createDeleteForm($service->getId(), 'service_delete');

        return array(
            'service' => $service,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Service entity.
     *
     * @Route("/{id}/update", name="service_update", requirements={"id"="\d+"})
     * @Method("PUT")
     * @Template("FlowerCoreBundle:Service:edit.html.twig")
     */
    public function updateAction(Service $service, Request $request)
    {
        $editForm = $this->createForm(new ServiceType(), $service, array(
            'action' => $this->generateUrl('service_update', array('id' => $service->getid())),
            'method' => 'PUT',
        ));
        if ($editForm->handleRequest($request)->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('service_show', array('id' => $service->getId())));
        }
        $deleteForm = $this->createDeleteForm($service->getId(), 'service_delete');

        return array(
            'service' => $service,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    /**
     * Save order.
     *
     * @Route("/order/{field}/{type}", name="service_sort")
     */
    public function sortAction($field, $type)
    {
        $this->setOrder('service', $field, $type);

        return $this->redirect($this->generateUrl('service'));
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
     * Deletes a Service entity.
     *
     * @Route("/{id}/delete", name="service_delete", requirements={"id"="\d+"})
     * @Method("DELETE")
     */
    public function deleteAction(Service $service, Request $request)
    {
        $form = $this->createDeleteForm($service->getId(), 'service_delete');
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($service);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('service'));
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
