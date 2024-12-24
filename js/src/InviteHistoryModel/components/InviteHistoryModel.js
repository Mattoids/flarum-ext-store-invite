import Modal from 'flarum/common/components/Modal';

export default class InviteHistoryModel extends Modal {
  className() {
    return 'Modal--small InviteHistoryModel';
  }

  title() {
    return app.translator.trans('mattoid-store-invite._');
  }

  content() {
    return (
      <div className="Modal-body">
        // See https://docs.flarum.org/2.x/extend/interactive-components.html#modals for more information.
      </div>
    );
  }
}
