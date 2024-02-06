import { uniqueId } from 'lodash';

interface MenuitemsType {
  [x: string]: any;
  id?: string;
  navlabel?: boolean;
  subheader?: string;
  title?: string;
  icon?: any;
  href?: string;
  children?: MenuitemsType[];
  chip?: string;
  chipColor?: string;
  variant?: string;
  external?: boolean;
}
import {
  IconAward,
  IconBoxMultiple,
  IconPoint,
  IconAlertCircle,
  IconNotes,
  IconCalendar,
  IconMail,
  IconTicket,
  IconEdit,
  IconGitMerge,
  IconCurrencyDollar,
  IconApps,
  IconFileDescription,
  IconFileDots,
  IconFiles,
  IconBan,
  IconStar,
  IconMoodSmile,
  IconBorderAll,
  IconBorderHorizontal,
  IconBorderInner,
  IconBorderVertical,
  IconBorderTop,
  IconUserCircle,
  IconPackage,
  IconMessage2,
  IconBasket,
  IconChartLine,
  IconChartArcs,
  IconChartCandle,
  IconChartArea,
  IconChartDots,
  IconChartDonut3,
  IconChartRadar,
  IconLogin,
  IconUserPlus,
  IconRotate,
  IconBox,
  IconShoppingCart,
  IconAperture,
  IconLayout,
  IconSettings,
  IconHelp,
  IconZoomCode,
  IconBoxAlignBottom,
  IconBoxAlignLeft,
  IconBorderStyle2,
  IconAppWindow
} from '@tabler/icons';
import CategoryOutlinedIcon from '@mui/icons-material/CategoryOutlined';
import DensitySmallOutlinedIcon from '@mui/icons-material/DensitySmallOutlined';
import MenuBookOutlinedIcon from '@mui/icons-material/MenuBookOutlined';
import BallotIcon from '@mui/icons-material/Ballot';
import FeaturedPlayListOutlinedIcon from '@mui/icons-material/FeaturedPlayListOutlined';
const userType = localStorage.getItem('userType');
let Menuitems=[];

if(userType === '2') {
Menuitems = [
  {
    navlabel: true,
    subheader: 'Home',
  },
  {
    id: uniqueId(),
    title: 'Dashboard',
    icon: IconAperture,
    href: '/super-admin/dashboard',
    chipColor: 'secondary',
  },
  {
    id: uniqueId(),
    title: 'Society Admin Management',
    icon: IconUserCircle,
    href: '/super-admin/society-admin-list',
    activeUrls: ['/super-admin/society-admin-list',`/super-admin/add-society-admin`,`/super-admin/edit-society-admin/*`],
  },
  {
    id: uniqueId(),
    title: 'Society Management',
    icon: IconAppWindow,
    href: '/super-admin/society-list',
    activeUrls: ['/super-admin/society-list',`/super-admin/add-society`,`/super-admin/edit-society/*`],
  },
  {
    id: uniqueId(),
    title: 'Subscription Plan',
    icon: IconPackage,
    href: '/super-admin/subscription-plan-list',
    activeUrls: ['/super-admin/subscription-plan-list',`/super-admin/add-subscription`,`/super-admin/add-subscription/*`],
  },
  {
    id: uniqueId(),
    title: 'Setting',
    icon: IconSettings,
    href: '/super-admin/system-setting',
    activeUrls: ['/super-admin/system-setting'],
  },
  {
    id: uniqueId(),
    title: 'Email Template',
    icon: IconMail,
    href: '/super-admin/email-template',
    activeUrls: ['/super-admin/email-template'],
  },
];
}else if(userType === '1'){
  Menuitems = [
    {
      navlabel: true,
      subheader: 'Home',
    },
    {
      id: uniqueId(),
      title: 'Dashboard',
      icon: IconAperture,
      href: '/admin/dashboard',
      chipColor: 'secondary',
    },
    {
      navlabel: true,
      subheader: 'Society Management',
    },
    {
      id: uniqueId(),
      title: 'Tower',
      icon: IconMessage2,
      href: '/admin/tower-list',
      activeUrls: ['/admin/tower-list', '/admin/add-tower', '/admin/edit-tower/*'],
    },
    {
      id: uniqueId(),
      title: 'Floor',
      icon: IconBorderVertical,
      href: '/admin/floor-list',
      activeUrls: ['/admin/floor-list', '/admin/add-floor', '/admin/edit-floor/*'],
    },
    {
      id: uniqueId(),
      title: 'Flat',
      icon: IconBox,
      href: '/admin/flat-list',
      activeUrls: ['/admin/flat-list', '/admin/add-flat', '/admin/edit-flat/*'],
    },
    {
      id: uniqueId(),
      title: 'Parking',
      icon: IconChartDots,
      href: '/admin/parking-list',
      activeUrls: ['/admin/parking-list', '/admin/add-parking', '/admin/edit-parking/*'],
    },
  ];
}else if(userType === '0'){
  Menuitems = [
    {
      navlabel: true,
      subheader: 'Home',
    },
    {
      id: uniqueId(),
      title: 'Dashboard',
      icon: IconAperture,
      href: '/user/dashboard',
      chipColor: 'secondary',
    },
  ];
}
export default Menuitems;
