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
