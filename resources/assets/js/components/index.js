import authorView from './AuthorView';
import authUser from './AuthUser';
import formError from './FormError';
import readerView from './ReaderView';
import authorDashboard from './AuthorDashboard';
import readerDashboard from './ReaderDashboard';
import progress from './Progress';

// User
import userSettings from './user/UserSettings';
import userKyc from './user/Kyc';

// Book
import bookCreate from './book/Create';
import bookView from './book/View';
import manuscriptUpload from './book/ManuscriptUpload';
import bookReaderView from './book/Reader';
import bookBuy from './book/Buy';

// UI
import uiFormField from './ui/formField';

export default [
    {
        tag: 'author-view',
        comp: authorView
    },
    {
        tag: 'auth-user',
        comp: authUser
    },
    {
        tag: 'author-dashboard',
        comp: authorDashboard
    },
    {
        tag: 'reader-dashboard',
        comp: readerDashboard
    },
    {
        tag: 'form-error',
        comp: formError
    },
    {
        tag: 'user-settings',
        comp: userSettings
    },
    {
        tag: 'reader-view',
        comp: readerView
    },
    {
        tag: 'progress',
        comp: progress
    },
    {
        tag: 'book-create',
        comp: bookCreate
    },
    {
        tag: 'book-view',
        comp: bookView
    },
    {
        tag: 'ui-form-field',
        comp: uiFormField
    },
    {
        tag: 'kyc',
        comp: userKyc
    },
    {
        tag: 'manuscript-upload',
        comp: manuscriptUpload
    },
    {
        tag: 'book-reader',
        comp: bookReaderView
    },
    {
        tag: 'book-buy',
        comp: bookBuy
    }
];
