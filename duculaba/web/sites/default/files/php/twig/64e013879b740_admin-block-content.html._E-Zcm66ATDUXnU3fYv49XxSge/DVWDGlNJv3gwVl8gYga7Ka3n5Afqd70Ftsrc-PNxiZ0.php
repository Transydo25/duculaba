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

/* themes/contrib/bootstrap5_admin/templates/admin/admin-block-content.html.twig */
class __TwigTemplate_39cb3900e9bc735b5a97a980cc26a734 extends \Twig\Template
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
        // line 20
        $context["classes"] = [0 => "list-group", 1 => "list-group-flush", 2 => ((        // line 23
($context["compact"] ?? null)) ? ("compact") : (""))];
        // line 26
        if (($context["content"] ?? null)) {
            // line 27
            echo "  <ul";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 27), 27, $this->source), "html", null, true);
            echo ">
    ";
            // line 28
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["content"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                // line 29
                echo "    <li class=\"list-group-item\">
      <h5 class=\"list-group__link card-title\">";
                // line 30
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "link", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
                echo "</h5>
      ";
                // line 31
                if (twig_get_attribute($this->env, $this->source, $context["item"], "description", [], "any", false, false, true, 31)) {
                    // line 32
                    echo "        <p class=\"list-group__description blockquote-footer m-2\">";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, $context["item"], "description", [], "any", false, false, true, 32), 32, $this->source), "html", null, true);
                    echo "</p>
      ";
                }
                // line 34
                echo "    </li>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 36
            echo "  </ul>
";
        }
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap5_admin/templates/admin/admin-block-content.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  75 => 36,  68 => 34,  62 => 32,  60 => 31,  56 => 30,  53 => 29,  49 => 28,  44 => 27,  42 => 26,  40 => 23,  39 => 20,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Default theme implementation for the content of an administrative block.
 *
 * Available variables:
 * - content: A list containing information about the block. Each element
 *   of the array represents an administrative menu item, and must at least
 *   contain the keys 'title', 'link_path', and 'localized_options', which are
 *   passed to l(). A 'description' key may also be provided.
 * - attributes: HTML attributes to be added to the element.
 * - compact: Boolean indicating whether compact mode is turned on or not.
 *
 * @see template_preprocess_admin_block_content()
 *
 * @ingroup themeable
 */
#}
{%
  set classes = [
    'list-group',
    'list-group-flush',
    compact ? 'compact',
  ]
%}
{% if content %}
  <ul{{ attributes.addClass(classes) }}>
    {% for item in content %}
    <li class=\"list-group-item\">
      <h5 class=\"list-group__link card-title\">{{ item.link }}</h5>
      {% if item.description %}
        <p class=\"list-group__description blockquote-footer m-2\">{{ item.description }}</p>
      {% endif %}
    </li>
    {% endfor %}
  </ul>
{% endif %}
", "themes/contrib/bootstrap5_admin/templates/admin/admin-block-content.html.twig", "/var/www/duculaba/web/themes/contrib/bootstrap5_admin/templates/admin/admin-block-content.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 20, "if" => 26, "for" => 28);
        static $filters = array("escape" => 27);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if', 'for'],
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
