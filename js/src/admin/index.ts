import app from 'flarum/admin/app';
import InviteGoodsForm from "./components/InviteGoodsForm";
import settingsFields from "./components/InviteSettingsFields";

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  // 主动注册到商店插件的管理端组件注册表：
  // hideDefaultDesc: 商店"添加/编辑商品"弹窗不显示默认"商品描述"字段（描述由本拓展设置项管理）
  const adminRegistry = (app as any).storeAdminRegistry;
  if (adminRegistry) {
    adminRegistry.goodsForm.register('invite', InviteGoodsForm, { hideDefaultDesc: true });
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
