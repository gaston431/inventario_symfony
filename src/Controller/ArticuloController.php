<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Articulo;
use App\Entity\Movimiento;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticuloController extends AbstractController
{
    /**
     * @Route("/", name="articulos")
     */
    public function index(ManagerRegistry $doctrine): Response
    {   
        $articulos = $doctrine->getRepository(Articulo::class)->findAll();
        
        return $this->render('articulo/index.html.twig', [
            'articulos' => $articulos
        ]);
    }

    /**
     * @Route("/articulos/{id}/movimientos", name="movimientos_articulo")
     */
    public function movimientos(ManagerRegistry $doctrine, int $id): Response
    {   
        $articulo = $doctrine->getRepository(Articulo::class)->find($id);

        $movimientos = $articulo->getMovimientos();
        
        return $this->render('articulo/movimientos.html.twig', [
            'movimientos' => $movimientos
        ]);
    }

    /**
     * @Route("/api/articulos", name="api_articulos", methods={"GET"})
     */
    public function articulos(ManagerRegistry $doctrine): Response
    {   
        $articulos = $doctrine->getRepository(Articulo::class)->findAll();
        
        $data = [];
        foreach ($articulos as $articulo) {
            $data[] = [
                'id' => $articulo->getId(),
                'numero' => $articulo->getNumero(),
                'descripcion' => $articulo->getDescripcion(),
                'inventario' => $articulo->getInventario(),
                'ubicacion' => $articulo->getUbicacion()
            ];
        }

        return $this->json(['data' => $data]);

    }

    /**
     * @Route("/api/articulos/{id}/movimientos", name="api_movimientos_articulo", methods={"GET"})
     */
    public function getMovimientosArticulo(ManagerRegistry $doctrine, int $id): Response
    {   
        $articulo = $doctrine->getRepository(Articulo::class)->find($id);

        $movimientos = $articulo->getMovimientos();
        
        $data = [];
        foreach ($movimientos as $movimiento) {
            
            $data[] = [
                'id' => $movimiento->getId(),
                'cantidad' => $movimiento->getCantidad(),
                'tipo' => $movimiento->getTipo(),
                'fecha' => $movimiento->getFecha(),
                'articulo_id' => $movimiento->getArticulo()->getId()
            ];
        }

        return $this->json(['data' => $data]);

    }

    /**
     * @Route("/api/articulo", name="add_articulo", methods={"POST"})
     */
    public function addArticulo(Request $request, ManagerRegistry $doctrine): Response
    {   
        $data = $request->request->all();
        
        $numero = $data['numero'];
        $descripcion = $data['descripcion'];
        $ubicacion = $data['ubicacion'];
        
        if (empty($numero) || empty($descripcion) || empty($ubicacion)) {
            throw new NotFoundHttpException('No se encontraron datos obligatorios: numero, descripcion y/o ubicacion.');
        }

        $entityManager = $doctrine->getManager();

        $articulo = new Articulo();
        $articulo->setNumero($numero);
        $articulo->setDescripcion($descripcion);
        $articulo->setUbicacion($ubicacion);
        
        $entityManager->persist($articulo);
        $entityManager->flush();

        return $this->json(['status' => 'Articulo creado! id: '. $articulo->getId()]);
    }

    /**
     * @Route("/api/movimiento", name="add_movimiento", methods={"POST"})
     */
    public function addMovimiento(Request $request, ManagerRegistry $doctrine): Response
    {   
        $data = $request->request->all();
        
        $cantidad = $data['cantidad'];
        $tipo = $data['tipo'];
        $articulo_id = $data['articulo_id'];
        
        if ($cantidad < 0 || empty($tipo) || empty($articulo_id)) {
            throw new NotFoundHttpException('No se encontraron datos obligatorios: cantidad, tipo y/o articulo_id.');
        }

        $articulo = $doctrine->getRepository(Articulo::class)->find($articulo_id);

        if (!$articulo) {
            throw new NotFoundHttpException('El articulo no existe!');
        }

        $entityManager = $doctrine->getManager();

        $movimiento = new Movimiento();
        $movimiento->setCantidad($cantidad);
        $movimiento->setFecha(new \DateTime);
        $movimiento->setTipo($tipo);
        
        $movimiento->setArticulo($articulo);

        //actualizamos el inventario(stock) del articulo segun tipo
        switch ($tipo) {
            case 'compra':
                $cant = $articulo->getInventario() + $cantidad;
                break;
            case 'venta':
                if ($articulo->getInventario() < $cantidad) {
                    throw new NotFoundHttpException('No hay stock!');
                }
                $cant = $articulo->getInventario() - $cantidad;
                break;
            case 'recuento':
                $cant = $cantidad;
                break;
            default:
                $cant = $articulo->getInventario();
                break;
        }

        $articulo->setInventario($cant);

        $entityManager->persist($movimiento);
        $entityManager->flush();

        return $this->json(['status' => 'Movimiento creado! id: ' . $movimiento->getId()]);
    }
}
