<?php

namespace MunicipioExtended\Model;

class Model implements \ArrayAccess {
  protected $data;

  public function getDefaultArrayMapping(): array {
    return array_keys($this->data);
  }

  public function __construct($data = []) {
    $this->data = $data;
  }

  public static function create(string $class, ...$args): Model {
    $namespaces = ["MunicipioExtended\\Model\\", "MuPlugin\\Model\\"];

    /**
     * Filters the namespaces searched when creating a Municipio Extended model.
     *
     * @param string[] $namespaces Model namespaces.
     * @param string   $class      Short model class name.
     * @param array    $args       Arguments passed to the model constructor.
     * @return string[] Filtered model namespaces.
     */
    $namespaces = apply_filters(
      "mx/model/namespaces",
      $namespaces,
      $class,
      $args,
    );
    $namespaces = array_reverse($namespaces);
    $full_class = null;
    foreach ($namespaces as $namespace) {
      if (class_exists($namespace . $class)) {
        $full_class = $namespace . $class;
        break;
      }
    }

    /**
     * Filters the resolved model class before it is instantiated.
     *
     * @param class-string|null $full_class Resolved model class, or null when none matched.
     * @param string            $class      Short model class name.
     * @return class-string|null Filtered model class.
     */
    $full_class = apply_filters("mx/model/class", $full_class, $class);
    if (!$full_class) {
      throw new \InvalidArgumentException("No model class found for $class");
    }
    return new $full_class(...$args);
  }

  public function has(string $name): bool {
    $method_name = "get" . ucfirst($name);
    if (method_exists($this, $method_name)) {
      $reflection = new \ReflectionMethod($this, $method_name);
      return $reflection->isPublic();
    }
    return isset($this->data[$name]);
  }

  public function get(string $name): mixed {
    $method_name = "get" . ucfirst($name);
    if (method_exists($this, $method_name)) {
      $reflection = new \ReflectionMethod($this, $method_name);
      if ($reflection->isPublic()) {
        return $this->$method_name();
      }
    }
    return $this->data[$name] ?? null;
  }

  public function __isset(string $name): bool {
    return $this->has($name);
  }

  public function __get(string $name): mixed {
    return $this->get($name);
  }

  public function offsetExists(mixed $offset): bool {
    $prop_name = acf_str_camel_case($offset);
    return isset($this->$prop_name);
  }

  public function offsetGet(mixed $offset): mixed {
    $prop_name = acf_str_camel_case($offset);
    return $this->$prop_name;
  }

  public function offsetSet(mixed $offset, mixed $value): void {
    throw new \BadMethodCallException("Setting properties is not allowed");
  }

  public function offsetUnset(mixed $offset): void {
    throw new \BadMethodCallException("Unsetting properties is not allowed");
  }

  public function toArray($mapping = null): array {
    if (!$mapping) {
      $mapping = $this->getDefaultArrayMapping();
    }
    $data = [];
    foreach ($mapping as $key => $value) {
      if (is_string($key)) {
        $data[$key] = $this->$value;
      } else {
        $data[$value] = $this->$value;
      }
    }
    return $data;
  }
}
