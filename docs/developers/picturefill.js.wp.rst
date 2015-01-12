Picturefill.WP
==============

Updating the plugin
-------------------

Picturefill is included here as a `git subtree`_ following the instructions `here <http://blogs.atlassian.com/2013/05/alternatives-to-git-submodule-git-subtree/>`_.

Our copy of Picturefill.WP is using the `1.3.x`_ branch of that plugin.

To update the copy of Picturefill.WP in ``inc/picturefill`` while remaining on ``1.3.x``::

	git subtree pull --prefix inc/picturefill https://github.com/kylereicks/picturefill.js.wp.git 1.3.x --squash

To update to the `master`_ branch, which may break functionality in Largo::

	git subtree pull --prefix inc/picturefill https://github.com/kylereicks/picturefill.js.wp.git master --squash

.. _git subtree: https://github.com/git/git/blob/master/contrib/subtree/git-subtree.txt
.. _1.3.x: https://github.com/kylereicks/picturefill.js.wp/tree/1.3.x
.. _master: https://github.com/kylereicks/picturefill.js.wp/tree/master
