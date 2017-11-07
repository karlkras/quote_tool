<?php
/**
 * @file
 *  The EnumSet class, which implements a collection of enumerated type
 *  elements as part of providing enumerated type support in PHP.
 *
 *  2010 Red Bottle Design, LLC. All rights reserved.
 *
 *  http://www.redbottledesign.com
 *
 *  This source code is free software: you can redistribute it and/or modify
 *  it under the terms of the Lesser GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  Lesser GNU General Public License for more details.
 *
 *  You should have received a copy of the Lesser GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Guy Paddock (guy.paddock@redbottledesign.com)
 */

/**
 * A collection of the elements for a particular enumerated type. Instances
 * of this type are returned by <code>Enum::declareElements()</code>.
 *
 * Once instantiated, an enum element collection is immutable -- its member
 * fields cannot be modified externally.
 *
 * @author Guy Paddock (guy.paddock@redbottledesign.com)
 *
 */
final class EnumSet
implements Iterator
{
  /**
   * The elements of the collection.
   *
   * @var unknown_type
   */
  private $elements;

  /**
   * Constructor for EnumSet.
   *
   * Initializes a new instance of <code>EnumSet</code> for the specified array
   * of enumerated type elements.
   *
   * @param array $elements
   *              The array of enumerated type elements.
   */
  public function EnumSet(array $elements)
  {
    $this->checkElements($elements);

    $this->elements = $elements;
  }

  /**
   * Return the elements in this collection as an array of <code>Enum</code>
   * instances.
   *
   * @return  array The elements of this collection as an array.
   */
  public function toArray()
  {
    // Clone the array so that the internal array pointer is not exposed
    return ($this->cloneArray($this->elements));
  }

  /**
   * Return the enumerated type element with the specified name.
   *
   * @param string  $elementName
   *                The name of the desired element, as it was declared when the
   *                enumerated type was initialized.
   *
   * @return        Enum
   *                The enum element with the provided name, or
   *                <code>NULL</code> if no element with the provided name
   *                exists in this collection.
   */
  public function getElement($name)
  {
    if (isset($this->elements[$name]))
      $result = $this->elements[$name];

    else
      $result = NULL;

    return $result;
  }

  /**
   * Return the value of the enumerated type element with the specified name.
   *
   * @param string  $elementName
   *                The name of the desired element, as it was declared when the
   *                enumerated type was initialized.
   *
   * @return        mixed
   *                The value of the element with the provided name, or
   *                <code>NULL</code> if no element with the provided name
   *                exists in this collection.
   */
  public function valueOf($elementName)
  {
    $element = $this->getElement($elementName);

    if (!empty($element))
      $result = $element->getValue();

    else
      $result = NULL;

    return $result;
  }

  /**
   * Return the first enum element that has the specified value.
   *
   * @param   mixed $value
   *                The value associated with the enum element that is to be
   *                returned.
   *
   * @return        Enum
   *                The enum element with the specified value, or
   *                <code>NULL</code> if an enum element with the specified
   *                value does not exist in this type.
   */
  public function elementForValue($value)
  {
    $result  = NULL;

    foreach ($this->elements as $element)
    {
      if ($element->getValue() === $value)
      {
        $result = $element;
        break;
      }
    }

    return ($result);
  }

  /**
   * Return the enumerated type element that the internal iterator currently
   * points to in this collection.
   *
   * @see Iterator::current()
   *
   * @return  Enum
   *          The current enumerated type element.
   */
  public function current()
  {
    return current($this->elements);
  }

  /**
   * Return the enumerated type element name that the internal iterator currently
   * points to in this collection.
   *
   * @see Iterator::key()
   *
   * @return  string
   *          The "key" (i.e. name) of the current enumerated type element.
   */
  public function key()
  {
    return key($this->elements);
  }

  /**
   * Move the internal iterator forward to point to the next enumerated type
   * element in this collection.
   *
   * @see Iterator::next()
   */
  public function next()
  {
    next($this->elements);
  }

  /**
   * Rewind the internal iterator back to point at the first enumerated type
   * element in this collection.
   *
   * @see Iterator::rewind()
   */
  public function rewind()
  {
    reset($this->elements);
  }

  /**
   * Check if internal iterator current position is valid.
   *
   * @see Iterator::valid()
   *
   * @return  boolean
   *          <code>TRUE</code> if the current position of the internal
   *          iterator points to a valid enumerated type element;
   *          <code>FALSE</code> otherwise.
   */
  public function valid()
  {
    return ($this->current() !== FALSE);
  }

  /**
   * A convenience overload for allowing access to enumerated type elements
   * in the collection by their declared name. For example,
   * $collection->MONDAY, instead of $collection->getElement('MONDAY').
   *
   * @param string  $name
   *                The name of the undeclared property of this object that
   *                is being accessed. If an enumerated type element with this
   *                name exists in the collection, it will be returned.
   *
   * @return        Enum
   *                The enumerated type element that has the specified name in
   *                this collection, or <code>NULL</code> if no element with
   *                the provided name exists.
   */
  public function __get($name)
  {
    return ($this->getElement($name));
  }

  /**
   * Overload to prevent modification of the enumerated element collection.
   *
   * @param string  $name
   *                The name of the undeclared property of this object that
   *                is being modified.
   *
   * @param mixed   $value
   *                The value to which the property is being set.
   *
   * @throws        RuntimeException
   *                If this method is invoked, as <code>EnumSets</code> are
   *                immutable.
   */
  public function __set($name, $value)
  {
    throw new RuntimeException('Member fields cannot be modified. EnumSet instances are immutable.');
  }

  /**
   * Return an enum set containing only the enum elements with the specified
   * names.
   *
   * @param string  ...
   *                Variable number of arguments; each one must be either an
   *                enum element instance, or a string matching the name of one
   *                of the enum elements in this collection.
   *
   * @return        EnumSet
   *                A new <code>EnumSet</code> containing only the specified
   *                enum elements.
   *
   * @throws        InvalidArgumentException
   *                If any of the provided enum elements are not of the right
   *                type (string, <code>Enum</code>) or do not match any of the
   *                enum elements in this collection.
   */
  public function of()
  {
    $parameters   = func_get_args();
    $newElements  = array();

    foreach ($parameters as $parameter)
    {
      $element      = $this->convertElementParameter($parameter, '');
      $elementName  = $element->getName();

      $newElements[$elementName] = $this->elements[$elementName];
    }

    return new EnumSet($newElements);
  }

  /**
   * Return an enum set containing only the range of enum elements that
   * start and end with the specified elements (inclusive).
   *
   * @param   mixed $from
   *                The element that should start the desired range of enum
   *                elements.
   *
   *                This can be either an enum element instance, or a string
   *                matching the name of one of the enum elements in this
   *                collection.
   *
   * @param   mixed $to
   *                The element that should end the desired range of enum
   *                elements.
   *
   *                This can be either an enum element instance, or a string
   *                matching the name of one of the enum elements in this
   *                collection.
   *
   * @return        EnumSet
   *                A new <code>EnumSet</code> containing only the specified
   *                range of enum elements.
   *
   * @throws        InvalidArgumentException
   *                If either of the provided elements are not of the right type
   *                (string, <code>Enum</code>), are not in this collection, or
   *                it is not possible to construct a range that starts from the
   *                index of <code>$from</code> and includes <code>$to</code>.
   */
  public function range($from, $to)
  {
    $fromElement = $this->convertElementParameter($from, '$from');
    $toElement   = $this->convertElementParameter($to, '$to');

    $newElements  = array();
    $fromSeen     = FALSE;
    $toSeen       = FALSE;

    foreach ($this->elements as $name => $element)
    {
      if (!$fromSeen && ($fromElement == $element))
        $fromSeen = TRUE;

      if ($fromSeen)
      {
        $newElements[$name] = $element;

        if ($toElement == $element)
        {
          $toSeen = TRUE;
          break;
        }
      }
    }

    if (!$toSeen)
    {
      throw new InvalidArgumentException(
                    "It is not possible to construct a range that starts from \"{$fromElement->getName()}\" and ".
                    "ends at \"{$toElement->getName()}\".");
    }

    return new EnumSet($newElements);
  }

  /**
   * Check the provided array of enumerated type elements, ensuring that all
   * elements are instances of <code>Enum</code>.
   *
   * @param array $elements
   *              The array of elements to check.
   *
   * @throws      InvalidArgumentException
   *              If any of values in the array is not an instance of
   *              <code>Enum</code>, or is not from the same enumerated type.
   */
  private function checkElements(array $elements)
  {
    $lastEnumClassName = NULL;

    foreach ($elements as $element)
    {
      /* @var $element Enum */
      if (!($element instanceof Enum))
      {
        throw new InvalidArgumentException('An EnumSet can contain only instances of Enum.');
      }

      else
      {
        $elementEnumClassName = $element->getEnumClassName();

        if ($lastEnumClassName == NULL)
        {
          $lastEnumClassName = $elementEnumClassName;
        }

        elseif ($lastEnumClassName != $elementEnumClassName)
        {
          throw new InvalidArgumentException(
                      'A single EnumSet instance cannot mix elements from different enumerated types.');
        }
      }
    }
  }

  /**
   * Handles translation of a parameter that could be either a string element
   * name or an <code>Enum</code> instance into the appropriate
   * <code>Enum</code> instance from this collection.
   *
   * @param mixed   $parameter
   *                The parameter value that needs conversion.
   *
   * @param string  $parameterName
   *                An optional parameter that specifies the name of the
   *                parameter that needs conversion, for use in validation
   *                exception messages.
   *
   * @throws        InvalidArgumentException
   *                If the specified parameter value is not one of the expected
   *                types (string, <code>Enum</code>), or does not correspond to
   *                an element in this collection.
   *
   * @return        The <code>Enum</code> element that the parameter value
   *                corresponds to.
   */
  private function convertElementParameter($parameter, $parameterName = NULL)
  {
    if (!empty($parameterName))
      $parameterName .= ' ';

    if (is_string($parameter))
    {
      $element = $this->elements[$parameter];
    }

    elseif ($parameter instanceof Enum)
    {
      $element = $parameter;
    }

    else
    {
      throw new InvalidArgumentException(
                  "The {$parameterName}parameter must be either an Enum instance or an element name string.");
    }

    if (empty($element) || ($this->elements[$element->getName()] !== $element))
    {
        throw new InvalidArgumentException(
                    "The value provided for the {$parameterName}parameter was not found in this collection.");
    }

    return $element;
  }

  /**
   * Clone an array by creating a new array that contains the same keys and
   * values as the provided array.
   *
   * @param array $array
   *              The array to clone.
   *
   * @return      array
   *              The resulting clone array.
   */
  private function cloneArray(array $array)
  {
    $newArray = array();

    foreach ($array as $key => $value)
    {
      $newArray[$key] = $value;
    }

    return $newArray;
  }
}