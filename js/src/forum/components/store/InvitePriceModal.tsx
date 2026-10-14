import app from 'flarum/forum/app';
import Modal, {IInternalModalAttrs} from 'flarum/common/components/Modal';
import InviteBuyForm from './InviteBuyForm';
import InviteNoPermissionView from './InviteNoPermissionView';

/**
 * 邀请商品购买弹窗（注册到 app.storeRegistry.priceModal，code = invite）
 *
 * 按购买权限分发两种内容：
 *  - 有权限（canInviteView）：渲染申请表单（InviteBuyForm，含购买按钮）
 *  - 无权限：渲染无权限描述视图（InviteNoPermissionView，无购买按钮）
 *
 * attrs:
 *  - storeData: 商店商品数据
 *  - onSuccess?: 购买成功回调
 */
interface IInvitePriceModalAttrs extends IInternalModalAttrs {
  storeData: any;
  onSuccess?: (storeData: any) => void;
}

export default class InvitePriceModal extends Modal<IInvitePriceModalAttrs> {
  private storeData: any = {};

  oninit(vnode) {
    super.oninit(vnode);

    this.storeData = this.attrs.storeData;
  }

  title() {
    return this.storeData.title || app.translator.trans('mattoid-store-invite.forum.title');
  }

  className(): string {
    return this.storeData.className || 'store-buy Modal--small';
  }

  content() {
    if (!this.canBuyInvite()) {
      return (
        <div className="Modal-body">
          <InviteNoPermissionView storeData={this.storeData}/>
        </div>
      );
    }

    return (
      <div className="Modal-body">
        <InviteBuyForm storeData={this.storeData} onSuccess={this.attrs.onSuccess}/>
      </div>
    );
  }

  /**
   * 当前用户是否拥有邀请商品购买权限（由 UserAttributes 输出的 canInviteView 决定）
   */
  canBuyInvite(): boolean {
    const user = app.session.user;
    if (!user) {
      return false;
    }
    return !!user.attribute('canInviteView');
  }
}
