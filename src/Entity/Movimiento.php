<?php

namespace App\Entity;

use App\Repository\MovimientoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MovimientoRepository::class)
 */
class Movimiento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $cantidad;

    const STATUS_COMPRA = 'compra';
    const STATUS_VENTA = 'venta';
    const STATUS_RECUENTO = 'recuento';

    /**
     * @ORM\Column(type="string", length=255, columnDefinition="ENUM('compra', 'venta', 'recuento')")
     */
    private $tipo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\ManyToOne(targetEntity=Articulo::class, inversedBy="movimientos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articulo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {   
        if (!in_array($tipo, array(self::STATUS_COMPRA, self::STATUS_VENTA, self::STATUS_RECUENTO))) {
            throw new \InvalidArgumentException("Invalid tipo");
        }

        $this->tipo = $tipo;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getArticulo(): ?Articulo
    {
        return $this->articulo;
    }

    public function setArticulo(?Articulo $articulo): self
    {
        $this->articulo = $articulo;

        return $this;
    }
}
