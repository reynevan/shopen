<?php

namespace Shopen\Core;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class BlockDirective
{
    public function handle($expression)
    {
        $data = explode(',', $expression) ?? '[]';
        array_shift($data);
        $data = implode(',', $data);
        if (!strlen($data)) { $data = '[]';}
        $blockDirectiveClass = self::class;
        return "<?php if (\$__env->exists(app('{$blockDirectiveClass}')->getView($expression))) echo \$__env->make(app('{$blockDirectiveClass}')->getView($expression), ['block' => app(app('{$blockDirectiveClass}')->getBlockClass($expression), ['data' => $data ])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>";
    }

    public function getView($expression)
    {
        $context = App::make(Context::class);
        $group = $context->isAdmin() ? 'Admin' : 'Frontend';
        $viewPath = str_replace("'", '', explode(',', $expression)[0]);
        $className = $this->getBlockClass($expression);
        $view = $className::getView();
        if (!$view) {
            $view =  'blocks.' . strtolower($group) . '.' . $viewPath;
        }
        if (!view()->exists($view)) {
            $pathParts = explode('.', $viewPath);
            $defaultView = 'blocks.' . strtolower($group) . '.' . $viewPath . '.' . array_pop($pathParts);
            if (view()->exists($defaultView)) {
                return $defaultView;
            }
            $defaultView = 'shopen::' . $defaultView;
            if (view()->exists($defaultView)) {
                return $defaultView;
            }
        }
        if (!view()->exists($view)) {
            $view = 'shopen::' . $view;
        }
        return $view;
    }

    public function getBlockClass($expression)
    {
        $context = App::make(Context::class);
        $group = $context->isAdmin() ? 'Admin' : 'Frontend';
        $viewPath = str_replace("'", '', explode(',', $expression)[0]);
        $className = $this->getClassNameFromTemplatePath($viewPath);
        $baseClassName = "App\Blocks\\$group\\$className";
        $shopenClassName = "Shopen\Blocks\\$group\\$className";
        if (class_exists($baseClassName)) {
            return $baseClassName;
        } elseif (class_exists($shopenClassName)) {
            return $shopenClassName;
        } else {
            return 'Shopen\\Blocks\\Block';
        }
    }

    protected function getClassNameFromTemplatePath($path)
    {
        $path = implode('', array_map('ucfirst', explode('-', $path)));
        return implode('\\', array_map('ucfirst', explode('.', $path)));
    }

}