import {FC} from 'react';
import {AppState, useSelector} from '@src/store/Store';
import {Link} from 'react-router-dom';
import {styled} from '@mui/material';
import img1 from '@src/assets/images/logos/society.png';
import img2 from '@src/assets/images/logos/ms.png';

const Logo: FC = () => {
  const customizer = useSelector((state: AppState) => state.customizer);
  const LinkStyled = styled(Link)(() => ({
    //height: customizer.TopbarHeight,
    width: customizer.isCollapse ? '40px' : '280px',
    overflow: 'hidden',
    display: 'block',
  }));

  if (customizer.activeDir === 'ltr') {
    return (
      <LinkStyled to="/">
        {/* {customizer.activeMode === 'dark' ? (
          <LogoLight height={customizer.TopbarHeight} />
        ) : (
          <LogoDark height={customizer.TopbarHeight} />
        )} */}
        {customizer.isCollapse ? 
        <img
          src={img2}
          alt="bg"
          style={{
            width: '100%',
            padding:'5px',
          }}
        />
        : 
          <img
            src={img1}
            alt="bg"
            style={{
              width: '100%',
              padding:'0px',
            }}
          />
        }
      </LinkStyled>
    );
  }

  return (
    <LinkStyled to="/">
      {/*{customizer.activeMode === 'dark' ? (*/}
      {/*  <LogoDarkRTL height={customizer.TopbarHeight} />*/}
      {/*) : (*/}
      {/*  <LogoLightRTL height={customizer.TopbarHeight} />*/}
      {/*)}*/}
      {customizer.isCollapse ? 
      <img
        src={img2}
        alt="bg"
        style={{
          width: '100%',
          padding:'5px',
        }}
      />
      : 
        <img
          src={img1}
          alt="bg"
          style={{
            width: '100%',
            padding:'0px',
          }}
        />
      }
    </LinkStyled>
  );
};

export default Logo;
