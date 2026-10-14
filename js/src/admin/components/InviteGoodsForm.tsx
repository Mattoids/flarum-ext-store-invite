import app from 'flarum/admin/app';
import Component from 'flarum/common/Component';

/**
 * 邀请商品在商店"添加/编辑商品"弹窗中的注册扩展区
 * （注册到 app.storeAdminRegistry.goodsForm，code = invite）
 *
 * 注册后商店弹窗会隐藏默认的"商品描述"字段——
 * 邀请商品的商品描述改由本拓展的设置项管理（有权限/无权限两份描述），
 * 此处仅提示管理员前往拓展设置页维护描述内容。
 */
export default class InviteGoodsForm extends Component {
  view() {
    return (
      <div className="StoreAdminField StoreInvite-AdminForm">
        <label>{app.translator.trans('mattoid-store-invite.admin.settings.goods-desc-section')}</label>
        <div className="helpText">
          {app.translator.trans('mattoid-store-invite.admin.settings.goods-desc-hint')}
        </div>
      </div>
    );
  }
}
