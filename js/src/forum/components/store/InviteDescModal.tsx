import app from 'flarum/forum/app';
import Modal, {IInternalModalAttrs} from 'flarum/common/components/Modal';

/**
 * 商品介绍完整展示弹窗
 * 点击商店页商品卡片上的商品介绍区域时打开，展示完整描述内容。
 *
 * attrs:
 *  - storeData: 商店商品数据
 *  - desc: 要展示的完整描述文本
 */
interface IInviteDescModalAttrs extends IInternalModalAttrs {
  storeData: any;
  desc: string;
}

export default class InviteDescModal extends Modal<IInviteDescModalAttrs> {
  title() {
    return (this.attrs.storeData && this.attrs.storeData.title)
      || app.translator.trans('mattoid-store-invite.forum.title');
  }

  className(): string {
    return 'StoreInvite-DescModal Modal--small';
  }

  content() {
    return (
      <div className="Modal-body">
        <div className="StoreInvite-DescModal-content">{this.attrs.desc}</div>
      </div>
    );
  }
}
