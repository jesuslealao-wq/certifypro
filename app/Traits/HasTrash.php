<?php

namespace App\Traits;

trait HasTrash
{
    /**
     * Mostrar elementos en la papelera
     */
    public function papelera()
    {
        $modelClass = $this->getModelClass();
        $items = $modelClass::onlyTrashed()->paginate(15);
        $viewName = $this->getViewName();
        
        return view("{$viewName}.papelera", [
            strtolower(class_basename($modelClass)) . 's' => $items
        ]);
    }

    /**
     * Restaurar un elemento eliminado
     */
    public function restore($id)
    {
        $modelClass = $this->getModelClass();
        $item = $modelClass::withTrashed()->findOrFail($id);
        $item->restore();
        
        $routeName = $this->getRouteName();
        return redirect()->route("{$routeName}.papelera")
            ->with('success', 'Elemento restaurado exitosamente.');
    }

    /**
     * Eliminar permanentemente un elemento
     */
    public function forceDelete($id)
    {
        $modelClass = $this->getModelClass();
        $item = $modelClass::withTrashed()->findOrFail($id);
        $item->forceDelete();
        
        $routeName = $this->getRouteName();
        return redirect()->route("{$routeName}.papelera")
            ->with('success', 'Elemento eliminado permanentemente.');
    }

    /**
     * Obtener la clase del modelo
     */
    abstract protected function getModelClass(): string;

    /**
     * Obtener el nombre de la vista
     */
    abstract protected function getViewName(): string;

    /**
     * Obtener el nombre de la ruta
     */
    abstract protected function getRouteName(): string;
}
