import app from 'flarum/forum/app';

import {extend} from 'flarum/common/extend';
import InvitePage from "./components/page/InvitePage";
import MyInvitePage from "./components/page/MyInvitePage";
import UserPage from 'flarum/forum/components/UserPage';
import IndexPage from 'flarum/forum/components/IndexPage';
import LinkButton from 'flarum/common/components/LinkButton';

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  app.routes.invite = {
    path: '/invite',
    component: InvitePage,
  };
  app.routes.myInvitePage = {
    path: '/u/:username/invite',
    component: MyInvitePage,
  };

  extend(UserPage.prototype, 'navItems', function (items) {
    items.add('myInvitePage', LinkButton.component({
      href: app.route('myInvitePage', {
        username: this.user.slug(),
      }),
      icon: 'fas fa-money-bill',
    }, app.translator.trans('mattoid-store-invite.forum.my-invite')));
  });

  extend(IndexPage.prototype, 'navItems', function (items) {
    if (!app.session.user.attribute('canInviteAdminView') || app.forum.attribute("inviteShowIndex") == 0) {
      return false;
    }

    items.add('invite', LinkButton.component({
      href: app.route('invite'),
      icon: 'fas fa-store',
    }, app.translator.trans('mattoid-store-invite.forum.invite')));
  });
});
