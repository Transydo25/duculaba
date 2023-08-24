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

/* themes/contrib/bootstrap5_admin/templates/form/form-element.html.twig */
class __TwigTemplate_8f97a5863e8c577e44cfc288b0867f8b extends \Twig\Template
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
        // line 48
        $context["classes"] = [0 => "js-form-item", 1 => "form-item", 2 => ("js-form-type-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 51
($context["type"] ?? null), 51, $this->source))), 3 => ("form-type-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(        // line 52
($context["type"] ?? null), 52, $this->source))), 4 => ((        // line 53
($context["name"] ?? null)) ? (("js-form-item-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["name"] ?? null), 53, $this->source)))) : ("")), 5 => ((        // line 54
($context["name"] ?? null)) ? (("form-item-" . \Drupal\Component\Utility\Html::getClass($this->sandbox->ensureToStringAllowed(($context["name"] ?? null), 54, $this->source)))) : ("")), 6 => ((!twig_in_filter(        // line 55
($context["title_display"] ?? null), [0 => "after", 1 => "before"])) ? ("form-no-label") : ("")), 7 => (((twig_in_filter(        // line 56
($context["title_display"] ?? null), [0 => "after", 1 => "before"]) && !twig_in_filter(($context["type"] ?? null), [0 => "radio", 1 => "radios", 2 => "checkbox", 3 => "checkboxes"]))) ? ("mb-2") : ("")), 8 => (((twig_in_filter(        // line 57
($context["title_display"] ?? null), [0 => "after", 1 => "before"]) && twig_in_filter(($context["type"] ?? null), [0 => "radio", 1 => "radios", 2 => "checkbox", 3 => "checkboxes"]))) ? ("form-check") : ("")), 9 => (((        // line 58
($context["disabled"] ?? null) == "disabled")) ? ("form-disabled") : ("")), 10 => ((        // line 59
($context["errors"] ?? null)) ? ("form-item--error") : ("")), 11 => ((((        // line 60
($context["type"] ?? null) == "item") && ($context["name"] ?? null))) ? ("row") : (""))];
        // line 64
        $context["description_classes"] = [0 => "description", 1 => "form-text", 2 => (((        // line 67
($context["description_display"] ?? null) == "invisible")) ? ("visually-hidden") : (""))];
        // line 70
        echo "<div";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [0 => ($context["classes"] ?? null)], "method", false, false, true, 70), 70, $this->source), "html", null, true);
        echo ">
  ";
        // line 71
        if (twig_in_filter(($context["label_display"] ?? null), [0 => "before", 1 => "invisible"])) {
            // line 72
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["label"] ?? null), 72, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 74
        echo "  ";
        if (((($context["prefix"] ?? null) || ($context["suffix"] ?? null)) && !twig_in_filter(($context["name"] ?? null), [0 => "label", 1 => "name", 2 => "settings-label", 3 => "site_frontpage"]))) {
            // line 75
            echo "  <div class=\"input-group\">
  ";
        }
        // line 77
        echo "  ";
        if ( !twig_test_empty(($context["prefix"] ?? null))) {
            // line 78
            echo "    <span class=\"field-prefix ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar((((($context["type"] ?? null) == "number")) ? ("input-group-text") : ("form-text")));
            echo "\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["prefix"] ?? null), 78, $this->source), "html", null, true);
            echo "</span>
  ";
        }
        // line 80
        echo "  ";
        if (((($context["description_display"] ?? null) == "before") && twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 80))) {
            // line 81
            echo "    <div";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "attributes", [], "any", false, false, true, 81), 81, $this->source), "html", null, true);
            echo ">
      ";
            // line 82
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 82), 82, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 85
        echo "  ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["children"] ?? null), 85, $this->source), "html", null, true);
        echo "
  ";
        // line 86
        if ( !twig_test_empty(($context["suffix"] ?? null))) {
            // line 87
            echo "    <span class=\"field-suffix ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar((((($context["type"] ?? null) == "number")) ? ("input-group-text") : ("form-text")));
            echo "\">";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["suffix"] ?? null), 87, $this->source), "html", null, true);
            echo "</span>
  ";
        }
        // line 89
        echo "
  ";
        // line 90
        if (((($context["prefix"] ?? null) || ($context["suffix"] ?? null)) && !twig_in_filter(($context["name"] ?? null), [0 => "label", 1 => "name", 2 => "settings-label", 3 => "site_frontpage"]))) {
            // line 91
            echo "  </div>
  ";
        }
        // line 93
        echo "  ";
        if ((($context["label_display"] ?? null) == "after")) {
            // line 94
            echo "    ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["label"] ?? null), 94, $this->source), "html", null, true);
            echo "
  ";
        }
        // line 96
        echo "  ";
        if (($context["errors"] ?? null)) {
            // line 97
            echo "    <div class=\"form-item--error-message\">
      ";
            // line 98
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["errors"] ?? null), 98, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 101
        echo "  ";
        if ((twig_in_filter(($context["description_display"] ?? null), [0 => "after", 1 => "invisible"]) && twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 101))) {
            // line 102
            echo "    <div";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "attributes", [], "any", false, false, true, 102), "addClass", [0 => ($context["description_classes"] ?? null)], "method", false, false, true, 102), 102, $this->source), "html", null, true);
            echo ">
      ";
            // line 103
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["description"] ?? null), "content", [], "any", false, false, true, 103), 103, $this->source), "html", null, true);
            echo "
    </div>
  ";
        }
        // line 106
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "themes/contrib/bootstrap5_admin/templates/form/form-element.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  158 => 106,  152 => 103,  147 => 102,  144 => 101,  138 => 98,  135 => 97,  132 => 96,  126 => 94,  123 => 93,  119 => 91,  117 => 90,  114 => 89,  106 => 87,  104 => 86,  99 => 85,  93 => 82,  88 => 81,  85 => 80,  77 => 78,  74 => 77,  70 => 75,  67 => 74,  61 => 72,  59 => 71,  54 => 70,  52 => 67,  51 => 64,  49 => 60,  48 => 59,  47 => 58,  46 => 57,  45 => 56,  44 => 55,  43 => 54,  42 => 53,  41 => 52,  40 => 51,  39 => 48,);
    }

    public function getSourceContext()
    {
        return new Source("{#
/**
 * @file
 * Theme override for a form element.
 *
 * Available variables:
 * - attributes: HTML attributes for the containing element.
 * - errors: (optional) Any errors for this form element, may not be set.
 * - prefix: (optional) The form element prefix, may not be set.
 * - suffix: (optional) The form element suffix, may not be set.
 * - required: The required marker, or empty if the associated form element is
 *   not required.
 * - type: The type of the element.
 * - name: The name of the element.
 * - label: A rendered label element.
 * - label_display: Label display setting. It can have these values:
 *   - before: The label is output before the element. This is the default.
 *     The label includes the #title and the required marker, if #required.
 *   - after: The label is output after the element. For example, this is used
 *     for radio and checkbox #type elements. If the #title is empty but the
 *     field is #required, the label will contain only the required marker.
 *   - invisible: Labels are critical for screen readers to enable them to
 *     properly navigate through forms but can be visually distracting. This
 *     property hides the label for everyone except screen readers.
 *   - attribute: Set the title attribute on the element to create a tooltip but
 *     output no label element. This is supported only for checkboxes and radios
 *     in \\Drupal\\Core\\Render\\Element\\CompositeFormElementTrait::preRenderCompositeFormElement().
 *     It is used where a visual label is not needed, such as a table of
 *     checkboxes where the row and column provide the context. The tooltip will
 *     include the title and required marker.
 * - description: (optional) A list of description properties containing:
 *    - content: A description of the form element, may not be set.
 *    - attributes: (optional) A list of HTML attributes to apply to the
 *      description content wrapper. Will only be set when description is set.
 * - description_display: Description display setting. It can have these values:
 *   - before: The description is output before the element.
 *   - after: The description is output after the element. This is the default
 *     value.
 *   - invisible: The description is output after the element, hidden visually
 *     but available to screen readers.
 * - disabled: True if the element is disabled.
 * - title_display: Title display setting.
 *
 * @see template_preprocess_form_element()
 */
#}
{%
  set classes = [
    'js-form-item',
    'form-item',
    'js-form-type-' ~ type|clean_class,
    'form-type-' ~ type|clean_class,
    name ? 'js-form-item-' ~ name|clean_class,
    name ? 'form-item-' ~ name|clean_class,
    title_display not in ['after', 'before'] ? 'form-no-label',
    title_display in ['after', 'before'] and type not in ['radio', 'radios' ,'checkbox', 'checkboxes'] ? 'mb-2',
    title_display in ['after', 'before'] and type in ['radio', 'radios' ,'checkbox', 'checkboxes'] ? 'form-check',
    disabled == 'disabled' ? 'form-disabled',
    errors ? 'form-item--error',
    type == 'item' and name ? 'row',
  ]
%}
{%
  set description_classes = [
    'description',
    'form-text',
    description_display == 'invisible' ? 'visually-hidden',
  ]
%}
<div{{ attributes.addClass(classes) }}>
  {% if label_display in ['before', 'invisible'] %}
    {{ label }}
  {% endif %}
  {% if (prefix or suffix) and name not in ['label', 'name', 'settings-label', 'site_frontpage'] %}
  <div class=\"input-group\">
  {% endif %}
  {% if prefix is not empty %}
    <span class=\"field-prefix {{ type == 'number' ? 'input-group-text' : 'form-text'}}\">{{ prefix }}</span>
  {% endif %}
  {% if description_display == 'before' and description.content %}
    <div{{ description.attributes }}>
      {{ description.content }}
    </div>
  {% endif %}
  {{ children }}
  {% if suffix is not empty %}
    <span class=\"field-suffix {{ type == 'number' ? 'input-group-text' : 'form-text'}}\">{{ suffix }}</span>
  {% endif %}

  {% if (prefix or suffix) and name not in ['label', 'name', 'settings-label', 'site_frontpage'] %}
  </div>
  {% endif %}
  {% if label_display == 'after' %}
    {{ label }}
  {% endif %}
  {% if errors %}
    <div class=\"form-item--error-message\">
      {{ errors }}
    </div>
  {% endif %}
  {% if description_display in ['after', 'invisible'] and description.content %}
    <div{{ description.attributes.addClass(description_classes) }}>
      {{ description.content }}
    </div>
  {% endif %}
</div>
", "themes/contrib/bootstrap5_admin/templates/form/form-element.html.twig", "/var/www/duculaba/web/themes/contrib/bootstrap5_admin/templates/form/form-element.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 48, "if" => 71);
        static $filters = array("clean_class" => 51, "escape" => 70);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['clean_class', 'escape'],
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
