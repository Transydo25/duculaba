<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/contrib/bootstrap5_admin/templates/form/input.html.twig */
class __TwigTemplate_297f4ca3b08ea107cfe140d3e42363b2 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 13
        echo "
";
        // line 14
        if (((twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", [0 => "button"], "method", false, false, true, 14) &&  !twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", [0 => "field-add-more-submit"], "method", false, false, true, 14)) &&  !twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", [0 => "btn"], "method", false, false, true, 14))) {
            // line 15
            echo "  ";
            // line 16
            $context["classes"] = [0 => "btn", 1 => ((twig_get_attribute($this->env, $this->source,             // line 18
($context["attributes"] ?? null), "hasClass", [0 => "button--danger"], "method", false, false, true, 18)) ? ("btn-danger") : ("")), 2 => ((( !twig_get_attribute($this->env, $this->source,             // line 19
($context["attributes"] ?? null), "hasClass", [0 => "media-library-item__remove"], "method", false, false, true, 19) &&  !twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", [0 => "button--danger"], "method", false, false, true, 19))) ? ("btn-primary") : (""))];
            // line 22
            echo "  ";
            if ((((($__internal_compile_0 = ($context["attributes"] ?? null)) && is_array($__internal_compile_0) || $__internal_compile_0 instanceof ArrayAccess ? ($__internal_compile_0["type"] ?? null) : null) == "submit") &&  !twig_test_empty(($context["icon"] ?? null)))) {
                // line 23
                echo "    <button";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 23), 23, $this->source), "html", null, true);
                echo ">";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["icon"] ?? null), 23, $this->source), "html", null, true);
                echo " ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed((($__internal_compile_1 = ($context["attributes"] ?? null)) && is_array($__internal_compile_1) || $__internal_compile_1 instanceof ArrayAccess ? ($__internal_compile_1["value"] ?? null) : null), 23, $this->source), "html", null, true);
                echo "</button>
  ";
            } else {
                // line 25
                echo "    <input";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 25), 25, $this->source), "html", null, true);
                echo " />
  ";
            }
        } elseif ((twig_get_attribute($this->env, $this->source,         // line 27
($context["attributes"] ?? null), "hasClass", [0 => "form-date"], "method", false, false, true, 27) || twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "hasClass", [0 => "form-time"], "method", false, false, true, 27))) {
            // line 28
            echo "  <input";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => "form-control"], "method", false, false, true, 28), 28, $this->source), "html", null, true);
            echo " />
";
        } else {
            // line 30
            echo "  <input";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 30, $this->source), "html", null, true);
            echo " />
";
        }
        // line 32
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["children"] ?? null), 32, $this->source), "html", null, true);
        echo "
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap5_admin/templates/form/input.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  83 => 32,  77 => 30,  71 => 28,  69 => 27,  63 => 25,  53 => 23,  50 => 22,  48 => 19,  47 => 18,  46 => 16,  44 => 15,  42 => 14,  39 => 13,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Theme override for an 'input' #type form element.
 *
 * Available variables:
 * - attributes: A list of HTML attributes for the input element.
 * - children: Optional additional rendered elements.
 *
 * @see template_preprocess_input()
 */
#}

{% if attributes.hasClass('button') and not attributes.hasClass('field-add-more-submit') and not attributes.hasClass('btn') %}
  {%
    set classes = [
      'btn',
      attributes.hasClass('button--danger') ? 'btn-danger' : '',
      (not attributes.hasClass('media-library-item__remove') and not attributes.hasClass('button--danger')) ? 'btn-primary' : ''
    ]
  %}
  {% if( attributes['type'] == 'submit' and icon is not empty) %}
    <button{{ attributes.addClass(classes) }}>{{ icon }} {{ attributes['value'] }}</button>
  {% else %}
    <input{{ attributes.addClass(classes) }} />
  {% endif %}
{% elseif attributes.hasClass('form-date') or attributes.hasClass('form-time')%}
  <input{{ attributes.addClass('form-control') }} />
{% else %}
  <input{{ attributes }} />
{% endif %}
{{ children }}
", "themes/contrib/bootstrap5_admin/templates/form/input.html.twig", "/var/www/duculaba/web/themes/contrib/bootstrap5_admin/templates/form/input.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 14, "set" => 16);
        static $filters = array("escape" => 23);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
