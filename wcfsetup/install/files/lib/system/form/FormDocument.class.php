<?php

namespace wcf\system\form;

use wcf\util\StringUtil;

/**
 * FormDocument holds the page structure based upon form element containers.
 *
 * @author  Alexander Ebert
 * @copyright   2001-2019 WoltLab GmbH
 * @license GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */
class FormDocument
{
    /**
     * list of FormElementContainer objects
     * @var IFormElementContainer[]
     */
    protected $containers = [];

    /**
     * form document name
     * @var string
     */
    protected $name = '';

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = StringUtil::trim($name);
    }

    /**
     * Returns form document name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Appends a FormElementContainer object.
     *
     * @param IFormElementContainer $container
     * @return void
     */
    public function appendContainer(IFormElementContainer $container)
    {
        $this->containers[] = $container;
    }

    /**
     * Prepends a FormElementContainer object.
     *
     * @param IFormElementContainer $container
     * @return void
     */
    public function prependContainer(IFormElementContainer $container)
    {
        \array_unshift($this->containers, $container);
    }

    /**
     * Returns assigned FormElementContainer objects.
     *
     * @return IFormElementContainer[]
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Returns the value of container's child element with given name.
     *
     * @param string $key
     * @return mixed
     */
    public function getValue($key)
    {
        foreach ($this->containers as $container) {
            $value = $container->getValue($key);
            if ($value !== null) {
                return $value;
            }
        }
    }

    /**
     * Returns HTML-representation of current document.
     *
     * @return string
     */
    public function getHTML()
    {
        $content = '';

        foreach ($this->containers as $container) {
            $content .= $container->getHTML($this->getName() . '_');
        }

        return $content;
    }

    /**
     * Handles request input variables.
     *
     * @return void
     */
    public function handleRequest()
    {
        $variables = [];

        foreach ($_REQUEST as $key => $value) {
            if (\str_contains($key, $this->getName() . '_')) {
                $key = \str_replace($this->getName() . '_', '', $key);
                $variables[$key] = $value;
            }
        }

        if (!empty($variables)) {
            foreach ($this->containers as $container) {
                $container->handleRequest($variables);
            }
        }
    }

    /**
     * Sets localized error message for given element.
     *
     * @param string $name
     * @param string $error
     * @return void
     */
    public function setError($name, $error)
    {
        foreach ($this->containers as $container) {
            $container->setError($name, $error);
        }
    }
}
