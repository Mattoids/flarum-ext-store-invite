import app from 'flarum/forum/app';
import Component from 'flarum/common/Component';

/**
 * 无购买权限用户的弹窗内容视图（无购买按钮）
 * attrs:
 *  - storeData: 商店商品数据
 */
export default class InviteNoPermissionView extends Component {
  private storeData: any = {};

  oninit(vnode) {
    super.oninit(vnode);

    this.storeData = this.attrs.storeData;
  }

  view() {
    const desc = app.forum.attribute('inviteGoodsNoPermissionDesc')
      || app.translator.trans('mattoid-store-invite.forum.goods-no-permission-desc');

    return (
      <div className="StoreInvite-NoPermission">
        {this.storeData.icon ? (
          <div className="StoreInvite-NoPermission-icon">
            <img src={this.storeData.icon}/>
          </div>
        ) : null}
        <div className="StoreInvite-NoPermission-desc">{desc}</div>
        <div className="StoreInvite-NoPermission-hint helpText">
          {app.translator.trans('mattoid-store-invite.forum.no-permission-hint')}
        </div>
      </div>
    );
  }
}
