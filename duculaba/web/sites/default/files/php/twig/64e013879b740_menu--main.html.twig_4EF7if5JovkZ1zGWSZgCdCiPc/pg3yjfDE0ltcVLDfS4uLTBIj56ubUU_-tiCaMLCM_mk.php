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

/* themes/contrib/bootstrap5_admin/templates/navigation/menu--main.html.twig */
class __TwigTemplate_cf8ee00ddae50b323952ae3acaf1c09b extends \Twig\Template
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
        // line 21
        $macros["menus"] = $this->macros["menus"] = $this;
        // line 22
        echo "
";
        // line 27
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_build_menu", [($context["items"] ?? null), ($context["attributes"] ?? null), 0], 27, $context, $this->getSourceContext()));
        echo "

";
        // line 43
        echo "
";
    }

    // line 29
    public function macro_build_menu($__items__ = null, $__attributes__ = null, $__menu_level__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "items" => $__items__,
            "attributes" => $__attributes__,
            "menu_level" => $__menu_level__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            // line 30
            echo "  ";
            $macros["menus"] = $this;
            // line 31
            echo "  ";
            if (($context["items"] ?? null)) {
                // line 32
                echo "  ";
                if ((($context["menu_level"] ?? null) == 0)) {
                    // line 33
                    echo "<ul";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => "navbar-nav me-auto"], "method", false, false, true, 33), 33, $this->source), "html", null, true);
                    echo ">
";
                } else {
                    // line 35
                    echo "<ul class=\"dropdown-menu\">
  ";
                }
                // line 37
                echo "  ";
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 38
                    echo "    ";
                    echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_add_link", [$context["item"], ($context["attributes"] ?? null), ($context["menu_level"] ?? null)], 38, $context, $this->getSourceContext()));
                    echo "
  ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 40
                echo "</ul>
";
            }

            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    // line 44
    public function macro_add_link($__item__ = null, $__attributes__ = null, $__menu_level__ = null, ...$__varargs__)
    {
        $macros = $this->macros;
        $context = $this->env->mergeGlobals([
            "item" => $__item__,
            "attributes" => $__attributes__,
            "menu_level" => $__menu_level__,
            "varargs" => $__varargs__,
        ]);

        $blocks = [];

        ob_start();
        try {
            // line 45
            echo "  ";
            $macros["menus"] = $this;
            // line 46
            echo "  ";
            $context["list_item_classes"] = [0 => "nav-item", 1 => (((twig_get_attribute($this->env, $this->source,             // line 48
($context["item"] ?? null), "is_expanded", [], "any", false, false, true, 48) && (($context["menu_level"] ?? null) != 1))) ? ("dropdown") : ("")), 2 => (((twig_get_attribute($this->env, $this->source,             // line 49
($context["item"] ?? null), "is_expanded", [], "any", false, false, true, 49) && twig_in_filter(($context["menu_level"] ?? null), [0 => 1, 1 => 2]))) ? ("dropend") : (""))];
            // line 51
            echo "  ";
            $context["link_class"] = [0 => (((            // line 52
($context["menu_level"] ?? null) == 0)) ? ("nav-item") : ("")), 1 => (((            // line 53
($context["menu_level"] ?? null) == 0)) ? ("nav-link") : ("")), 2 => ((twig_get_attribute($this->env, $this->source,             // line 54
($context["item"] ?? null), "in_active_trail", [], "any", false, false, true, 54)) ? ("active") : ("")), 3 => (((twig_get_attribute($this->env, $this->source,             // line 55
($context["item"] ?? null), "is_expanded", [], "any", false, false, true, 55) || twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "is_collapsed", [], "any", false, false, true, 55))) ? ("dropdown-toggle") : ("")), 4 => (((            // line 56
($context["menu_level"] ?? null) > 0)) ? ("dropdown-item") : (""))];
            // line 58
            echo "  ";
            $context["toggle_class"] = [];
            // line 59
            echo "  <li";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "attributes", [], "any", false, false, true, 59), "addClass", [0 => ($context["list_item_classes"] ?? null)], "method", false, false, true, 59), 59, $this->source), "html", null, true);
            echo ">
    ";
            // line 60
            if (twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "below", [], "any", false, false, true, 60)) {
                // line 61
                echo "      ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getLink($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "title", [], "any", false, false, true, 61), 61, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "url", [], "any", false, false, true, 61), 61, $this->source), ["class" => ($context["link_class"] ?? null), "role" => "button", "data-bs-toggle" => "dropdown", "data-bs-auto-close" => "outside", "aria-expanded" => "false", "title" => ((t("Expand menu") . " ") . $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "title", [], "any", false, false, true, 61), 61, $this->source))]), "html", null, true);
                echo "
      ";
                // line 62
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(twig_call_macro($macros["menus"], "macro_build_menu", [twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "below", [], "any", false, false, true, 62), ($context["attributes"] ?? null), (($context["menu_level"] ?? null) + 1)], 62, $context, $this->getSourceContext()));
                echo "
    ";
            } else {
                // line 64
                echo "      ";
                echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->extensions['Drupal\Core\Template\TwigExtension']->getLink($this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "title", [], "any", false, false, true, 64), 64, $this->source), $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["item"] ?? null), "url", [], "any", false, false, true, 64), 64, $this->source), ["class" => ($context["link_class"] ?? null)]), "html", null, true);
                echo "
    ";
            }
            // line 66
            echo "  </li>
";

            return ('' === $tmp = ob_get_contents()) ? '' : new Markup($tmp, $this->env->getCharset());
        } finally {
            ob_end_clean();
        }
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap5_admin/templates/navigation/menu--main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  170 => 66,  164 => 64,  159 => 62,  154 => 61,  152 => 60,  147 => 59,  144 => 58,  142 => 56,  141 => 55,  140 => 54,  139 => 53,  138 => 52,  136 => 51,  134 => 49,  133 => 48,  131 => 46,  128 => 45,  113 => 44,  102 => 40,  93 => 38,  88 => 37,  84 => 35,  78 => 33,  75 => 32,  72 => 31,  69 => 30,  54 => 29,  49 => 43,  44 => 27,  41 => 22,  39 => 21,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Theme override to display a menu.
 *
 * Available variables:
 * - menu_name: The machine name of the menu.
 * - items: A nested list of menu items. Each menu item contains:
 *   - attributes: HTML attributes for the menu item.
 *   - below: The menu item child items.
 *   - title: The menu link title.
 *   - url: The menu link url, instance of \\Drupal\\Core\\Url
 *   - localized_options: Menu link localized options.
 *   - is_expanded: TRUE if the link has visible children within the current
 *     menu tree.
 *   - is_collapsed: TRUE if the link has children within the current menu tree
 *     that are not currently visible.
 *   - in_active_trail: TRUE if the link is in the active trail.
 */
#}
{% import _self as menus %}

{#
We call a macro which calls itself to render the full tree.
@see http://twig.sensiolabs.org/doc/tags/macro.html
#}
{{ menus.build_menu(items, attributes, 0) }}

{% macro build_menu(items, attributes, menu_level) %}
  {% import _self as menus %}
  {% if items %}
  {% if menu_level == 0 %}
<ul{{ attributes.addClass('navbar-nav me-auto') }}>
{% else %}
<ul class=\"dropdown-menu\">
  {% endif %}
  {% for item in items %}
    {{ menus.add_link(item, attributes, menu_level) }}
  {% endfor %}
</ul>
{% endif %}
{% endmacro %}

{% macro add_link(item, attributes, menu_level) %}
  {% import _self as menus %}
  {% set list_item_classes = [
    'nav-item',
    (item.is_expanded and menu_level != 1)? 'dropdown',
    (item.is_expanded and menu_level in [1,2])? 'dropend',
  ] %}
  {% set link_class = [
    menu_level == 0 ? 'nav-item',
    menu_level == 0 ? 'nav-link',
    item.in_active_trail ? 'active',
    (item.is_expanded or item.is_collapsed) ? 'dropdown-toggle',
    menu_level > 0 ? 'dropdown-item',
  ] %}
  {% set toggle_class = [] %}
  <li{{ item.attributes.addClass(list_item_classes) }}>
    {% if item.below %}
      {{ link(item.title, item.url, { 'class': link_class, 'role': 'button', 'data-bs-toggle': 'dropdown', 'data-bs-auto-close': 'outside', 'aria-expanded': 'false', 'title': ('Expand menu' | t) ~ ' ' ~ item.title }) }}
      {{ menus.build_menu(item.below, attributes, (menu_level + 1)) }}
    {% else %}
      {{ link(item.title, item.url, { 'class': link_class }) }}
    {% endif %}
  </li>
{% endmacro %}
", "themes/contrib/bootstrap5_admin/templates/navigation/menu--main.html.twig", "/var/www/duculaba/web/themes/contrib/bootstrap5_admin/templates/navigation/menu--main.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("import" => 21, "macro" => 29, "if" => 31, "for" => 37, "set" => 46);
        static $filters = array("escape" => 33, "t" => 61);
        static $functions = array("link" => 61);

        try {
            $this->sandbox->checkSecurity(
                ['import', 'macro', 'if', 'for', 'set'],
                ['escape', 't'],
                ['link']
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
