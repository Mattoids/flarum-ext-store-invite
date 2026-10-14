import app from 'flarum/admin/app';
import InviteGoodsForm from "./components/InviteGoodsForm";
import settingsFields from "./components/InviteSettingsFields";

app.initializers.add('mattoid/flarum-ext-store-invite', () => {
  // 主动注册到商店插件的管理端组件注册表：
  // 注册后商店"添加/编辑商品"弹窗不再显示默认"商品描述"字段（描述由本拓展设置项管理）
  const adminRegistry = (app as any).storeAdminRegistry;
  if (adminRegistry) {
    adminRegistry.goodsForm.register('invite', InviteGoodsForm);
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

  // 邮件模板设置注册到 fof/pretty-mail 的设置页：
  // pretty-mail 使用标准 ExtensionPage，任何扩展都可向其注入设置项。
  // 设置键保持不变（mattoid-store-invite.mail*），后端无需改动。
  // priority 取负值，让两个字段排在 pretty-mail 自带模板编辑器之上，避免沉底。
  app.extensionData.for('fof-pretty-mail')
    .registerSetting({
      setting: 'mattoid-store-invite.mail.title',
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail-title-pretty'),
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-title-help'),
      type: 'text'
    }, -10)
    .registerSetting({
      setting: 'mattoid-store-invite.mail',
      label: app.translator.trans('mattoid-store-invite.admin.settings.mail-pretty'),
      help: app.translator.trans('mattoid-store-invite.admin.settings.mail-help'),
      placeholder: app.translator.trans('mattoid-store-invite.admin.settings.mail-template'),
      type: 'textarea',
      rows: 5
    }, -10);
});
