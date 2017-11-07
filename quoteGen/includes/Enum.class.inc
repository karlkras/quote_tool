<?php
/**
 * @file
 *  The Enum class, which implements enumerated types in PHP.
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
require_once('EnumSet.class.inc');

/**
 * An implementation of enumerated types in PHP.
 *
 * This implementation attempts to mimick Java's enumerated type implementation
 * wherever possible. It appears superior to other implementations in the
 * following ways:
 *
 *  - It does not use eval() at all, which means that code should be more
 *    predictable and much safer. Many other implementations rely on eval() to
 *    dynamically-generate classes at run-time, which can cause unexpected
 *    complications.
 *
 *  - It is type-safe. Implementors are guaranteed that the only Enum instances
 *    that can be created will be of the correct type, provided that each
 *    enumerated type is declared "final".
 *
 *  - It keeps the mechanics of the enumerated types fairly opaque to
 *    implementors, rather than relying on standard inheritance or
 *    caller-provided constant values. This makes the definition of enumerated
 *    types quick and straightforward, and eliminates the possibility that a
 *    rogue caller can fabricate an enumeration element that was not defined in
 *    the enumeration.
 *
 *  The non-static members of this class provide the functionality for single
 *  elements of enumerated types.
 *
 * @author Guy Paddock (guy.paddock@redbottledesign.com)
 */
abstract class Enum
{
  /**
   * An associative array that maps each enumerated type to its associated
   * array of elements. This allows us to enforce a singleton pattern, whereby
   * enumerated elements are instantiated only once during script execution,
   * which improves performance and guarantees that two enumeration elements
   * that represent the same value will always be strictly equivalent (===) to
   * one another.
   *
   * @var array
   */
  protected static $enumMap;

  /**
   * The name of the enumerated type in which this element is declared.
   *
   * @var string
   */
  protected $enumClassName;

  /**
   * The name of this element.
   *
   * @var string
   */
  protected $name;

  /**
   * The value associated with this particular element.
   *
   * @var string
   */
  protected $value;

  /**
   * Return the name of the enumerated type in which this element is declared.
   *
   * @return  string
   *          The name of the enumerated type in which this element has been
   *          declared.
   */
  public function getEnumClassName()
  {
    return $this->enumClassName;
  }

  /**
   * Return the name of this element.
   *
   * @return  string
   *          The name of this enum element, as it was declared when the
   *          parent enumerated type was initialized in a call to
   *          declareElements().
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * Return the value associated with this element.
   *
   * @return  mixed
   *          The value associated with this enum element, as it was declared
   *          when the parent enumerated type was initialized in a call to
   *          declareElements().
   */
  public function getValue()
  {
    return $this->value;
  }

  /**
   * Default __toString() implementation that uses the element name as its
   * string representation.
   *
   * @return  string
   *          The name of this element as its string representation.
   */
  public function __toString()
  {
    return $this->getName();
  }

  /**
   * Protected constructor that is used internally when initializing each
   * element of an enumerated type.
   *
   * Initializes an enum element with the specified enumerated type name,
   * element name, and value.
   *
   * Sub-classes should not call this constructor directly -- it should be
   * invoked through the static <code>declareElements()</code> method.
   *
   * @param string  $enumClassName
   *                The name of the enumerated type in which the new enum
   *                element is declared.
   *
   * @param string  $name
   *                The name for the new enum element.
   *
   * @param mixed   $value
   *                The value to associate with the new enum element.
   */
  protected function __construct($enumClassName, $name, $value)
  {
    $this->enumClassName  = $enumClassName;
    $this->name           = $name;
    $this->value          = $value;
  }

  /**
   * Return the value that is associated with the specified element of the
   * specified enumerated type.
   *
   * @param   string  $enumClassName
   *                  The class name of the enumerated type (i.e. the parent
   *                  type that contains the element).
   *
   * @param   string  $elementName
   *                  The name of the enum element whose value is to be
   *                  returned.
   *
   * @return          mixed
   *                  The value of the element with the specified name in the
   *                  specified enumerated type, or <code>NULL</code> if no enum
   *                  element with the specified name exists in this type.
   */
  public static function valueOf($enumClassName, $elementName)
  {
    if (isset(self::$enumMap[$enumClassName]))
    {
      $set    = new EnumSet(self::$enumMap[$enumClassName]);
      $result = $set->valueOf($elementName);
    }

    else
    {
      $result = NULL;
    }

    return $result;
  }

  /**
   * Return the first element of the specified enumerated type that has the
   * specified value.
   *
   * @param   string  $enumClassName
   *                  The class name of the enumerated type (i.e. the parent
   *                  type that contains the element).
   *
   * @param   mixed   $value
   *                  The value associated with the enum element that is to be
   *                  returned.
   *
   * @return          mixed
   *                  The element with the specified value in the specified
   *                  enumerated type, or <code>NULL</code> if no enum element
   *                  with the specified name exists in this type.
   */
  public static function elementForValue($enumClassName, $value)
  {
    if (isset(self::$enumMap[$enumClassName]))
    {
      $set    = new EnumSet(self::$enumMap[$enumClassName]);
      $result = $set->elementForValue($value);
    }

    else
    {
      $result = NULL;
    }

    return ($result);
  }

  /**
   * Method used by enumerated types to initialize their enumerated elements,
   * and return the resulting collection of enumerated type elements.
   *
   * This method implements a singleton pattern, which means that calling
   * it for the same enumerated type should result in the same collection of
   * enumerated type instances being returned.
   *
   * For consistency, this method should be called by sub-classes of
   * <code>Enum</code> in a static function called "enum()".
   *
   * For example:
   *
   * <code>
   *  class Day extends Enum {
   *    public static function enum() {
   *      return self::declareElements(
   *        __CLASS__,
   *        array(
   *          'MONDAY',           // Implicit value is 0
   *          'TUESDAY'   => 13,
   *          'WEDNESDAY',        // Implicit value is 14
   *          'THURSDAY',         // Implicit value is 15
   *          'FRIDAY',           // Implicit value is 16
   *          'SATURDAY'  => 'A Value',
   *          'SUNDAY'));         // Implicit value is 17
   *    }
   *  }
   * </code>
   *
   * @param   string  $enumClassName
   *                  The name of the enumerated type being initialized.
   *                  This should be provided using __CLASS__.
   *
   * @param   array   $elementData
   *                  An associative array that maps enumerated type elements to
   *                  values. If any elements are not mapped to values in the
   *                  array, they will be assigned ordinal values incrementally,
   *                  starting from either 0 or the value of the
   *                  last-encountered element having a numeric value.
   *
   * @return          EnumSet
   *                  A collection of the elements of the enumerated type.
   */
  protected static function declareElements($enumClassName, array $elementData)
  {
    if (isset(self::$enumMap[$enumClassName]))
    {
      $values = self::$enumMap[$enumClassName];
    }

    else
    {
      $values = array();

      foreach (self::arrangeElementData($elementData) as $elementName => $value)
      {
        // Can't use reflection here because of protected constructor
        $values[$elementName] = new $enumClassName($enumClassName, $elementName, $value);
      }

      self::$enumMap[$enumClassName] = $values;
    }

    return new EnumSet($values);
  }

  /**
   * Protected method used to adjust element data to account for elements that
   * have not been assigned a value.
   *
   * Without this method, the following enumerated elements:
   * <code>
   *  ET_EXAMPLE1 = 10, // Should have a value of 10
   *  ET_EXAMPLE2;      // Should have a value of 11
   * </code>
   *
   * ...would end up as an array like this:
   * <code>
   *  ET_EXAMPLE1 => 10
   *  0           => ET_EXAMPLE2
   * </code>
   *
   * Obviously, this is not the desired outcome. This method will properly
   * adjust the associative array of element data so that we have:
   *  ET_EXAMPLE1  => 10
   *  ET_EXAMPLE2  => 12
   *
   * @param   array $elementData
   *                The associative array of element data, which maps element
   *                names to values.
   *
   * @return  array The adjusted associative array of element data, in which
   *                all element names have been assigned an appropriate value.
   */
  protected static function arrangeElementData(array $elementData)
  {
    $lastNum        = -1;
    $newElementData = array();

    foreach ($elementData as $name => $value)
    {
      if (is_int($value))
        $lastNum  = $value;

      if (is_int($name))
        $newElementData[$value]  = ++$lastNum;

      else
        $newElementData[$name]   = $value;
    }

    return $newElementData;
  }
}