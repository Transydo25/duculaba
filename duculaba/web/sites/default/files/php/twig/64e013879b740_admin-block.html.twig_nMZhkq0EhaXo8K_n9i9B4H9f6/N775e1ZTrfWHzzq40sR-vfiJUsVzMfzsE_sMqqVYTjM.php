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

/* themes/contrib/bootstrap5_admin/templates/admin/admin-block.html.twig */
class __TwigTemplate_e08a1272256c082faa889e9dd950280f extends \Twig\Template
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
        // line 19
        $context["classes"] = [0 => "card", 1 => "mb-2"];
        // line 24
        echo "<details";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 24), 24, $this->source), "html", null, true);
        echo " open=\"open\">
  ";
        // line 25
        if (twig_get_attribute($this->env, $this->source, ($context["block"] ?? null), "title", [], "any", false, false, true, 25)) {
            // line 26
            echo "    <summary role=\"button\" aria-expanded=\"true\" aria-pressed=\"true\" class=\"panel__title card-header text-uppercase fs-5\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["block"] ?? null), "title", [], "any", false, false, true, 26), 26, $this->source), "html", null, true);
            echo "</summary>
  ";
        }
        // line 28
        echo "  ";
        if (twig_get_attribute($this->env, $this->source, ($context["block"] ?? null), "content", [], "any", false, false, true, 28)) {
            // line 29
            echo "  <div class=\"card-body\">
    <div class=\"panel__content card-text\">";
            // line 30
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["block"] ?? null), "content", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
            echo "</div>
  </div>
  ";
        } elseif (twig_get_attribute($this->env, $this->source,         // line 32
($context["block"] ?? null), "description", [], "any", false, false, true, 32)) {
            // line 33
            echo "    <footer class=\"panel__description blockquote-footer\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["block"] ?? null), "description", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
            echo "</footer>
  ";
        }
        // line 35
        echo "</details>
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap5_admin/templates/admin/admin-block.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 35,  67 => 33,  65 => 32,  60 => 30,  57 => 29,  54 => 28,  48 => 26,  46 => 25,  41 => 24,  39 => 19,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Default theme implementation for an administrative block.
 *
 * Available variables:
 * - block: An array of information about the block, including:
 *   - show: A flag indicating if the block should be displayed.
 *   - title: The block title.
 *   - content: (optional) The content of the block.
 *   - description: (optional) A description of the block.
 *     (Description should only be output if content is not available).
 * - attributes: HTML attributes for the containing div element.
 *
 * @ingroup themeable
 */
#}
{%
  set classes = [
    'card',
    'mb-2'
  ]
%}
<details{{ attributes.addClass(classes) }} open=\"open\">
  {% if block.title %}
    <summary role=\"button\" aria-expanded=\"true\" aria-pressed=\"true\" class=\"panel__title card-header text-uppercase fs-5\">{{ block.title }}</summary>
  {% endif %}
  {% if block.content %}
  <div class=\"card-body\">
    <div class=\"panel__content card-text\">{{ block.content }}</div>
  </div>
  {% elseif block.description %}
    <footer class=\"panel__description blockquote-footer\">{{ block.description }}</footer>
  {% endif %}
</details>
", "themes/contrib/bootstrap5_admin/templates/admin/admin-block.html.twig", "/var/www/duculaba/web/themes/contrib/bootstrap5_admin/templates/admin/admin-block.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 19, "if" => 25);
        static $filters = array("escape" => 24);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
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
