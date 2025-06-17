<?php

namespace App\Model;

/**
 * @author France Benoit
 */
interface ImageInterface
{
    /**
     * Returns the image unique id.
     *
     * @return mixed
     */
    public function getId();

    /**
     * Sets the image path.
     *
     * @param string $path
     *
     * @return self
     */
    public function setPath($path);

    /**
     * Gets the image path.
     *
     * @return string
     */
    public function getPath();

    /**
     * Sets the image alt attribute.
     *
     * @param string $path
     *
     * @return self
     */
    public function setAlt(string $alt, string $path = '');

    /**
     * Gets the image alt attribute.
     *
     * @return string
     */
    public function getAlt();

    /**
     * Sets the original image path.
     *
     * @param string $path
     *
     * @return self
     */
    public function setOriginalPath($path);

    /**
     * Gets the the original image path.
     *
     * @return string
     */
    public function getOriginalPath();

    /**
     * Sets the updated image datetime.
     *
     * @param datetime $updated
     *
     * @return self
     */
    public function setUpdated($updated);

    /**
     * Getsthe updated image datetime.
     *
     * @return string
     */
    public function getUpdated();
}
