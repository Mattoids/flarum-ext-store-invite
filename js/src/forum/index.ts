import app from 'flarum/forum/app';

import {extend} from 'flarum/common/extend';
import InvitePage from "./components/page/InvitePage";
import MyInvitePage from "./components/page/MyInvitePage";
import InviteGoodsCard from "./components/store/InviteGoodsCard";
import InvitePriceModal from "./components/store/InvitePriceModal";
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

  // 主动注册到商店插件的商品组件注册表（详见 flarum-ext-store 开发手册 5.1 节）
  // priceModal 注册为按权限分发的弹窗：有权限显示购买表单，无权限显示无权限描述（无购买按钮）
  const registry = (app as any).storeRegistry;
  if (registry) {
    registry.item.register('invite', InviteGoodsCard);
    registry.priceModal.register('invite', InvitePriceModal);
  }

  extend(UserPage.prototype, 'navItems', function (items) {
    items.add('myInvitePage', LinkButton.component({
      href: app.route('myInvitePage', {
        username: this.user.slug(),
      }),
      icon: 'fas fa-check-square',
    }, app.translator.trans('mattoid-store-invite.forum.my-invite')));
  });

  extend(IndexPage.prototype, 'navItems', function (items) {
    if (!app.session.user.attribute('canInviteAdminView') || app.forum.attribute("inviteShowIndex") == 0) {
      return false;
    }

    items.add('invite', LinkButton.component({
      href: app.route('invite'),
      icon: 'fas fa-calendar-check',
    }, app.translator.trans('mattoid-store-invite.forum.invite')));
  });
});
