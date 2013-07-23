<?php
namespace Meot\FormBundle\Twig;

class FormBuilderExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_simpleFilter('strip_username', array($this, 'stripUsernameFilter')),
        );
    }

    /**
     */
    /**
     * stripusernameFilter This function strips out the first part of username when the
     * username contains @
     *
     * @param string $username
     * @access public
     * @return string stripped username
     */
    public function stripusernameFilter($username) {
        $ret = explode("@", $username);

        return $ret[0];
    }

    public function getName()
    {
        return 'form_builder_extension';
    }
}
