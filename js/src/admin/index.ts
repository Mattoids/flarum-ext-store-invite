import app from 'flarum/admin/app';
import InviteGoodsForm from "./components/InviteGoodsForm";
import PrettyMailInviteFields from "./components/PrettyMailInviteFields";
import settingsFields from "./components/InviteSettingsFields";
import { extend } from 'flarum/common/extend';

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  // 主动注册到商店插件的管理端组件注册表：
  // 注册后商店"添加/编辑商品"弹窗不再显示默认"商品描述"字段（描述由本拓展设置项管理）
  const adminRegistry = (app as any).storeAdminRegistry;
  if (adminRegistry) {
    adminRegistry.goodsForm.register('invite', InviteGoodsForm);
  }

  // 邮件模板字段注入到 fof/pretty-mail 设置页：
  // pretty-mail 使用自定义页面（registerPage），标准 registerSetting 不会渲染，
  // 但其页面通过 templateItems() ItemList 渲染通知模板区，可安全扩展。
  // 字段复用 pretty-mail 页面的 setting() 流绑定，随其保存按钮一起提交。
  // 设置键保持不变（mattoid-store-invite.mail*），后端无需改动。
  const prettyMailPage = (app.extensionData as any).getPage('fof-pretty-mail');
  if (prettyMailPage) {
    extend(prettyMailPage.prototype, 'templateItems', function (items) {
      items.add('store-invite-mail', m(PrettyMailInviteFields, {page: this}));
    });
  }

  app.extensionData.for("mattoid-store-invite")
    // 设置页使用自定义分组布局（多字段合并一行），保存逻辑复用 ExtensionPage 原生机制
    .registerSetting(function () {
      return settingsFields(this);
    })
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-view'),
        permission: 'mattoid-store-invite.group-view',
        allowGuest: true
      }, 'view')
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-blacklist-view'),
        permission: 'mattoid-store-invite.group-blacklist-view',
        allowGuest: true
      }, 'view')
    .registerPermission(
      {
        icon: 'fas fa-id-card',
        label: app.translator.trans('mattoid-store-invite.admin.settings.group-admin-view'),
        permission: 'mattoid-store-invite.group-admin-view',
        allowGuest: true
      }, 'moderate')
});
