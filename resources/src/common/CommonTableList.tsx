import React, { useEffect, useState } from "react";
import { alpha, useTheme } from '@mui/material/styles';
import { format } from 'date-fns';
import * as XLSX from 'xlsx';
import Spinner from '@src/views/spinner/Spinner';
import {
  Box,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TablePagination,Pagination,
  TableRow,
  TableSortLabel,
  Toolbar,
  IconButton,
  Tooltip,
  FormControlLabel,
  Typography,
  Avatar,
  TextField,
  InputAdornment,
  Paper,
  Button,
  ListItemIcon,
  MenuItem, Card, CardHeader, CardContent, Divider,
  Menu,
  Dialog, DialogTitle, DialogContent, DialogContentText, DialogActions,Stack,DialogProps,
  Collapse,
  Chip,
  ButtonGroup,
  AvatarGroup,
} from '@mui/material';
import { visuallyHidden } from '@mui/utils';
import { useSelector, useDispatch } from '@src/store/Store';
import { fetchProducts } from '@src/store/apps/eCommerce/ECommerceSlice';
import CustomCheckbox from '@src/components/forms/theme-elements/CustomCheckbox';
import CustomSwitch from '@src/components/forms/theme-elements/CustomSwitch';
import { IconDotsVertical, IconFilter, IconSearch, IconTrash, IconChecks, IconEdit, IconZoomCode, IconMail, IconPlus, IconTicket, IconSettings } from '@tabler/icons';
import { ProductType } from '@src/types/apps/eCommerce';
import { Link } from 'react-router-dom';
import KeyboardArrowDownIcon from '@mui/icons-material/AddCircle';
import KeyboardArrowUpIcon from '@mui/icons-material/RemoveCircle';
import axios from "axios";
import CircularProgress from '@material-ui/core/CircularProgress';
import User1 from '@src/assets/images/profile/user-1.jpg';
import User3 from '@src/assets/images/profile/user-3.jpg';


function descendingComparator<T>(a: T, b: T, orderBy: keyof T) {
  if (b[orderBy] < a[orderBy]) {
    return -1;
  }
  if (b[orderBy] > a[orderBy]) {
    return 1;
  }

  return 0;
}

type Order = 'asc' | 'desc';

function getComparator<Key extends keyof any>(
  order: Order,
  orderBy: Key,
): (a: { [key in Key]: number | string }, b: { [key in Key]: number | string }) => number {
  return order === 'desc'
    ? (a, b) => descendingComparator(a, b, orderBy)
    : (a, b) => -descendingComparator(a, b, orderBy);
}

function stableSort<T>(array: T[], comparator: (a: T, b: T) => number) {
  const stabilizedThis = array.map((el, index) => [el, index] as [T, number]);
  stabilizedThis.sort((a, b) => {
    const order = comparator(a[0], b[0]);
    if (order !== 0) {
      return order;
    }

    return a[1] - b[1];
  });

  return stabilizedThis.map((el) => el[0]);
}

interface HeadCell {
  disablePadding: boolean;
  id: string;
  label: string;
  numeric: boolean;
  enableSorting: boolean;
  sortOrder: string;
}

interface EnhancedTableProps {
  numSelected: number;
  onRequestSort: (event: React.MouseEvent<unknown>, property: any) => void;
  onSelectAllClick: (event: React.ChangeEvent<HTMLInputElement>) => void;
  order: Order;
  orderBy: string;
  rowCount: number;
  rowHead: [];
  headCellsHidden: [];
}

function EnhancedTableHead(props: EnhancedTableProps) {
  const { onSelectAllClick, order, orderBy, numSelected, rowCount, rowHead, headCellsHidden, onRequestSort } = props;
  const createSortHandler = (property: any) => (event: React.MouseEvent<unknown>) => {
    onRequestSort(event, property);
  };
  
  const headCells: readonly HeadCell[] = rowHead;
  return (
    <TableHead>
      <TableRow>
        {typeof headCellsHidden !== 'undefined' && headCellsHidden .length > 0 && (
          <TableCell padding="checkbox"></TableCell>
        )}
         {/* checkbox functionality commented */}
        {/* <TableCell padding="checkbox">
          <CustomCheckbox
            color="primary"
            checked={rowCount > 0 && numSelected === rowCount}
            onChange={onSelectAllClick}
            inputProps={{
              'aria-label': 'select all desserts',
            }}
          />
          </TableCell> */}
        {headCells.map((headCell) => (
          <TableCell
            key={headCell.id}
            align={headCell.numeric ? 'right' : 'left'}
            padding={headCell.disablePadding ? 'none' : 'normal'}
            sortDirection={orderBy === headCell.id ? order : false}
          >
            {headCell.enableSorting === true ? (
            <TableSortLabel
              active={orderBy === headCell.id}
              direction={orderBy === headCell.id ? order : 'asc'}
              onClick={createSortHandler(headCell.id,headCell.sortOrder)}
            >
              {headCell.label}
              {orderBy === headCell.id ? (
                <Box component="span" sx={visuallyHidden}>
                  {order === 'desc' ? 'sorted descending' : 'sorted ascending'}
                </Box>
              ) : null}
            </TableSortLabel>
            ):(
              <>
                {headCell.label}
                {orderBy === headCell.id ? (
                  <Box component="span" sx={visuallyHidden}>
                    {order === 'desc' ? 'sorted descending' : 'sorted ascending'}
                  </Box>
                ) : null}
              </>
            )}

          </TableCell>
        ))}
      </TableRow>
    </TableHead>
  );
}

interface EnhancedTableToolbarProps {
  numSelected: number;
  handleSearchVal: React.ChangeEvent<HTMLInputElement> | any;
  search: string;
  dataRow:[];
  addUrl: string;
  handleExport: React.ChangeEvent<HTMLInputElement> | any;
  escalationList: React.ChangeEvent<HTMLInputElement> | any;
  headerButtons:[];
  handleAdd: React.ChangeEvent<HTMLInputElement> | any;
  totalCount: '';
  excelName: string;
  excelApiUrl: string;
  pageTitle:string;
  showSearch: string;
  isLoading: string;
  escalationStatus: string;
}

const EnhancedTableToolbar = (props: EnhancedTableToolbarProps) => {
const { numSelected, search, dataRow, handleSearchVal, addUrl, handleExport, totalCount, excelName, excelApiUrl, headerButtons, pageTitle, showSearch, isLoading, escalationList, escalationStatus } = props;

const [loading, setLoading] = useState(true);
const [showModal, setShowModal] = useState(false);
//Function for Excel Download
const downloadExcel = async () => {
  setLoading(true);
  setShowModal(true);
  try {
    const appUrl = import.meta.env.VITE_API_URL;
    const currentPath = window.location.pathname;

    let API_URL = appUrl + '/api/' + excelApiUrl;
    let method = 'post'; // Default method is POST

    // Check if the current path starts with the condition
    if (currentPath.startsWith('/admin/user-manual-section/')) {
      API_URL = appUrl + '/api/' + excelApiUrl; // Change API endpoint if condition is met
      method = 'get'; // Use GET method
    }

    const token = sessionStorage.getItem('authToken');
    
    const response = await axios({
      method: method,
      url: API_URL,
      headers: {
        Authorization: `Bearer ${token}`,
      },
      // If using GET method, data will be empty
      data: method === 'post' ? null : undefined,
    });

    if (response && response.data && response.data.data.file_url) {
      // Open the URL in a new tab
      window.open(response.data.data.file_url, '_blank');
    } else {
      alert('Error while downloading, please try again'); // Handle missing URL
    }
  } catch (error) {
    if (error.response && error.response.status === 401) {
      // Handle expired token error (Unauthorized)
      alert('Token Expired. Please login again.');
      // Redirect to login or perform a logout action
    } else {
      console.error("Error when downloading Excel:", error);
      alert('Error downloading Excel. Please try again.');
    }
  } finally {
    setLoading(false);
    setShowModal(false);
  }
};





//console.log('headerButtons:',headerButtons);
return (
  <>
  <Toolbar
    sx={{
      pl: { sm: 2 },
      pr: { xs: 1, sm: 1 },
      display: 'flex',
      alignItems: 'center',
      justifyContent: 'space-between',
    }}
  >
    <div><CardHeader title={props.pageTitle}/>
    </div>


<div >
    {(typeof headerButtons !== 'undefined' && typeof headerButtons.statusButtonOpen !== 'undefined' &&  headerButtons.statusButtonOpen != '') &&
    <Tooltip title="Open">
      <IconButton>
        <Button variant={escalationStatus =='0' ? "contained" : "outlined"} color="warning" type="button" onClick={() => escalationList('0')}>{headerButtons.statusButtonOpen.title}</Button>
      </IconButton>
    </Tooltip>
    }
    {(typeof headerButtons !== 'undefined' && typeof headerButtons.statusButtonClose !== 'undefined' &&  headerButtons.statusButtonClose != '') &&
    <Tooltip title="Closed">
      <IconButton>
        <Button variant={escalationStatus =='1' ? "contained" : "outlined"} color="success" type="button" onClick={() => escalationList('1')}>{headerButtons.statusButtonClose.title}</Button>
      </IconButton>
    </Tooltip>
    }
    {(typeof headerButtons !== 'undefined' && typeof headerButtons.statusButtonMissed !== 'undefined' &&  headerButtons.statusButtonMissed != '') &&
    <Tooltip title="Missed">
      <IconButton>
        <Button variant={escalationStatus =='2' ? "contained" : "outlined"} color="error" type="button" onClick={() => escalationList('2')}>{headerButtons.statusButtonMissed.title}</Button>
      </IconButton>
    </Tooltip>
    }

    {(addUrl) ?
    <>
    {addUrl == "1" ? (
                <Tooltip title="Add">
                    <IconButton>
                        <Button
                            onClick={() => props.handleAdd("")}
                            variant="contained"
                            color="secondary"
                            type="button"
                        >
                            Add
                        </Button>
                    </IconButton>
                </Tooltip>
            ) : (
                <Tooltip title="Add">
                    <IconButton>
                        <Link to={addUrl}>
                            <Button
                                variant="contained"
                                color="secondary"
                                type="button"
                            >
                                Add
                            </Button>
                        </Link>
                    </IconButton>
                </Tooltip>
            )}
    </>
    : 
    (typeof headerButtons !== 'undefined' && typeof headerButtons.syncdata !== 'undefined' &&  headerButtons.syncdata != '') ?
    <Tooltip title="Sync Data">
      <IconButton>
          <Button variant="contained" color="success" type="button" disabled={isLoading} onClick={() => handleExport()}>
            <Box
            display="flex"
            alignItems="center"
            justifyContent="center"
            gap={1}
            >
            Sync Data
            {/* {isLoading && <CircularProgress size={24} color="inherit" />} */}
            </Box>
          </Button>
      </IconButton>
    </Tooltip> : ''
    }
    { (typeof headerButtons !== 'undefined' && typeof headerButtons.import !== 'undefined' &&  headerButtons.import != '') ?
    <Tooltip title={headerButtons.import.title}>
        <IconButton>
            <Link to={headerButtons.import.url}>
                <Button
                    variant="contained"
                    color={headerButtons.import.color}
                    type="button"
                >
                    {headerButtons.import.title}
                </Button>
            </Link>
        </IconButton>
    </Tooltip>
    : ''
    }
    {excelApiUrl === "" ? ("") : (
      <Tooltip title="Export">
      <IconButton>
        <Button variant="contained" color="primary" type="button" onClick={() => downloadExcel()}>Excel</Button>
      </IconButton>
    </Tooltip>
    )}

      {showSearch && showSearch === "no" ? ("") : (
        <TextField
        sx={{
          margin: '10px'
        }}
          InputProps={{
            startAdornment: (
              <InputAdornment position="start">
                <IconSearch size="1.1rem" />
              </InputAdornment>
            ),
          }}
          placeholder="Search"
          size="small"
          onChange={handleSearchVal}
          value={search}
        />
      )}
    </div>

    {/* filter button commented as checkbox not applied */}
    {/* {numSelected > 0 ? (
      <Tooltip title="Delete">
        <IconButton>
          <IconTrash width="18" />
        </IconButton>
      </Tooltip>
    ) : (
      <Tooltip title="Filter list">
        <IconButton>
          <IconFilter size="1.2rem" />
        </IconButton>
      </Tooltip>
    )} */}
  </Toolbar>
  {showModal && (
    <Dialog
      open={showModal}
      onClose={() => {}}
      aria-labelledby="dialog-title"
      PaperProps={{
        style: {
          maxWidth: '400px',
          textAlign: 'center',
          padding: '20px',
        },
      }}
    >
      <DialogTitle id="dialog-title">Downloading Excel</DialogTitle>
      <DialogContent>
        <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center' }}>
          <CircularProgress color="primary" />
        </div>
        <p>Please wait while your download is in progress..</p>
      </DialogContent>
    </Dialog>
  )}
  </>
);
};

const CommonTableList = (props) => {
  const [order, setOrder] = React.useState<Order>();
  const [orderBy, setOrderBy] = React.useState<any>('calories');
  const [selected, setSelected] = React.useState<readonly string[]>([]);
  const [collapsed, setCollapsed] = React.useState<readonly string[]>([]);
  
  const userId = sessionStorage.getItem('userId');
  if(localStorage.getItem('currentURL') != window.location.href)//Condition to delete SortBy
  {
    localStorage.removeItem('sortByColumn');
    localStorage.removeItem('tablePaginationPage');
  }
  
  const [PaginationPage, setPaginationPage] = React.useState(() => {
    // Try to get the page state from local storage
    const savedPage = (localStorage.getItem('tablePaginationPage') > '0') ? localStorage.getItem('tablePaginationPage') : null;
    const currentURL = localStorage.getItem('currentURL');
    return (currentURL != window.location.href) ? 1 : savedPage ? parseInt(savedPage, 10) : 1;
  });
  //console.log('tablePaginationPage ----->',localStorage.getItem('tablePaginationPage'));
  //console.log('currentURL ----->',localStorage.getItem('currentURL'));
  //console.log('window.location.href TableList ----->',window.location.href);
  const [page, setPage] = React.useState(() => {
    // Try to get the page state from local storage tablePaginationPage
    const savedPage = localStorage.getItem('tablePaginationPage');
    const currentURL = localStorage.getItem('currentURL');
    return currentURL != window.location.href ? 0 : parseInt(savedPage, 10) > 0 ? parseInt(savedPage, 10) - 1 : 0;
  });
  //console.log('page :', page);
  
  (localStorage.getItem('currentURL') != window.location.href) ? localStorage.removeItem('rowsPerPage') : '';
  localStorage.setItem('currentURL',window.location.href);
  const [dense, setDense] = React.useState(true);
  //const [rowsPerPage, setRowsPerPage] = React.useState(10);
  const [rowsPerPage, setRowsPerPage] = React.useState(() => {
    // Try to get the page state from local storage tablePaginationPage
    const setrowsPerPage = localStorage.getItem('rowsPerPage');
    const currentURL = localStorage.getItem('currentURL');
    return (currentURL != window.location.href) ? 10 : setrowsPerPage ? parseInt(setrowsPerPage) : 10;
  });
  
  const rows = props.dataRow;
  const [search, setSearch] = React.useState('');

  const [anchorEl, setAnchorEl] = React.useState(null);
  const [clickedRowId, setClickedRowId] = React.useState(null);
  const [clickedRowData, setClickedRowData] = React.useState([]);
  const handleClickMenu = (event, row) => {
    setClickedRowId(row.id);
    setClickedRowData(row);
    setAnchorEl(event.currentTarget);
  };

  const handleCloseMenu = () => {
    setClickedRowId(null);
    setAnchorEl(null);
  };

  const handleSearchVal = (event: React.ChangeEvent<HTMLInputElement>) => {
      setSearch(event.target.value);
      const currentURL = localStorage.getItem('currentURL');
      (event.target.value.length == 0) ? sessionStorage.removeItem('searchKeyword') : sessionStorage.setItem("searchKeyword", event.target.value);
      setPage(0);
      setPaginationPage(1);
      localStorage.setItem('tablePaginationPage', '1');
      props.handleSearch(event.target.value,rowsPerPage);
  };
  
  // This is for the sorting
  const handleRequestSort = (event: React.MouseEvent<unknown>, property: any) => {
    let newOrder = 'asc'; // Default to ascending order if the property changes

    //alert('handleRequestSort order ->'+order);
    if (order == 'asc') {
      newOrder = 'desc'; // If currently ascending, change to descending
    }
    //alert('handleRequestSort newOrder ->'+newOrder);
    setOrder(newOrder);
    setOrderBy(property);
    //alert(column_name);
    localStorage.setItem('sortByColumn', property);//currentURL
    // Make an API call to fetch sorted data
    props.fetchData(rowsPerPage, PaginationPage, property, search, newOrder);
  };

  // This is for select all the row
  const handleSelectAllClick = (event: React.ChangeEvent<HTMLInputElement>) => {
    if (event.target.checked) {
      const newSelecteds = rows.map((n: any) => n.id);
      setSelected(newSelecteds);
      return;
    }
    setSelected([]);
  };

  // This is for the single row sleect
  const handleClick = (event: React.MouseEvent<unknown>, name: string, rowId) => {
    const selectedIndex = selected.indexOf(name);
    let newSelected: readonly string[] = [];

    if (selectedIndex === -1) {
      newSelected = newSelected.concat(selected, name);
    } else if (selectedIndex === 0) {
      newSelected = newSelected.concat(selected.slice(1));
    } else if (selectedIndex === selected.length - 1) {
      newSelected = newSelected.concat(selected.slice(0, -1));
    } else if (selectedIndex > 0) {
      newSelected = newSelected.concat(
        selected.slice(0, selectedIndex),
        selected.slice(selectedIndex + 1),
      );
    }
    setSelected(newSelected);
    // Pass the row.id to createSortHandler
    const isAsc = orderBy === name && order === 'asc';
    setOrder(isAsc ? 'desc' : 'asc');
    setOrderBy(name);

    // Now you have the `row.id` and `name` (column name) for sorting
    createSortHandler(name, rowId)(event);
  };

  const handleChangePage = (event: unknown, newPage: number) => {
    setPage(newPage);
    localStorage.setItem('tablePage', newPage);//currentURL
    localStorage.setItem('currentURL', window.location.href);
    var sortByCol = localStorage.getItem('sortByColumn') ? localStorage.getItem('sortByColumn') : 'id';
    
    //props.handleSearch(search);  
    props.fetchData(rowsPerPage, newPage+1, sortByCol, search, order);
  };
  
  const handleChangePagePagination = (event: unknown, newPage: number) => {
    setPage(newPage-1);
    setPaginationPage(newPage);
    localStorage.setItem('tablePaginationPage', newPage);//currentURL
    localStorage.setItem('tablePage', newPage-1);//currentURL
    localStorage.setItem('currentURL', window.location.href);
    var sortByCol = localStorage.getItem('sortByColumn') ? localStorage.getItem('sortByColumn') : 'id';
    //props.handleSearch(search);
    props.fetchData(rowsPerPage, newPage,sortByCol, search, order);
  };

  const handleChangeRowsPerPage = (event: React.ChangeEvent<HTMLInputElement>) => {
    props.fetchData(event.target.value, 1);
    setRowsPerPage(parseInt(event.target.value, 10));
    localStorage.setItem('rowsPerPage', parseInt(event.target.value, 10));
    setPage(0);
    setPaginationPage(1);
  };

  const handleChangeDense = (event: React.ChangeEvent<HTMLInputElement>) => {
    setDense(event.target.checked);
  };

  const isSelected = (name: string) => selected.indexOf(name) !== -1;

  // Avoid a layout jump when reaching the last page with empty rows.
  const emptyRows = page > 0 ? Math.max(0, (1 + page) * rowsPerPage - props.totalCount) : 0;

  const theme = useTheme();
  const borderColor = theme.palette.divider;
  const dataFields = props.dataColumns;
  const dataSettings = props.rowSettings;
  const collapseSettings = props.collapsibleColumns;
  const actionSettings = props.actionSettings;
  const editValue = actionSettings.actions.edit;
  const deleteValue = actionSettings.actions.delete;
  //Collapse Row
  const dataColumnsHidden = props.dataColumnsHidden;
  
  const [openRows, setOpenRows] = React.useState([]);

  const handleCollapse = (event: React.MouseEvent<unknown>, name: string) => {
    const selectedIndex = selected.indexOf(name);
    let newSelected: readonly string[] = [];
  
    if (selectedIndex === -1) {
      newSelected = newSelected.concat(selected, name);
    } else if (selectedIndex === 0) {
      newSelected = newSelected.concat(selected.slice(1));
    } else if (selectedIndex === selected.length - 1) {
      newSelected = newSelected.concat(selected.slice(0, -1));
    } else if (selectedIndex > 0) {
      newSelected = newSelected.concat(
        selected.slice(0, selectedIndex),
        selected.slice(selectedIndex + 1),
      );
    }
  
    setCollapsed(newSelected);
  
    // Toggle the open state of the clicked row
    if (openRows.includes(name)) {
      setOpenRows(openRows.filter((row) => row !== name));
    } else {
      setOpenRows([...openRows, name]);
    }
  };

  
  //Delete Dialog box
  const [open, setOpen] = React.useState(false);
  const [rowId, setRowId] = React.useState('');
  //Read More Dialog box
  const [readOpen, setReadOpen] = React.useState(false);
  const [readrowId, setReadRowId] = React.useState('');
  const [readmoreDesc, setReadmoreDesc] = React.useState('');
  const [readmoremodalHeading, modalHeading] = React.useState('');
  const [scroll, setScroll] = React.useState<DialogProps['scroll']>('paper');
  const handleClickOpen = (id) => {
      setOpen(true);
      setRowId(id);
  };
  const handleReadMore = (description,modalHead) => {
    modalHeading(modalHead);
    setReadmoreDesc(description);
    setReadOpen(true);
  };
  const handleClose = () => {
      setOpen(false);
  };

  // Function to set the initial sort order for a column
  const getColumnSortOrder = (columnId) => {
    const headCell = props.headCells.find((cell) => cell.id === columnId);
    return headCell && headCell.sortOrder ? headCell.sortOrder : 'asc';
  };

  // Effect to set initial order and orderBy based on headCells.sortOrder
  React.useEffect(() => {
    if (orderBy) {
      setOrder(getColumnSortOrder(orderBy));
    }
  }, [orderBy]);

  React.useEffect(() => {
    setOrder(props.pageSortOrder);
  }, [props.pageSortOrder]);

  return (
    <Box>
      <Box>
        <EnhancedTableToolbar
          pageTitle={props.pageTitle}
          numSelected={selected.length}
          search={search}
          handleSearchVal = {(event: any) => handleSearchVal(event)}
          dataRow = {rows}
          addUrl = {props.addUrl}
          handleAdd={props.handleAdd}
          handleExport = {props.handleExport}
          escalationList = {props.escalationList}
          headerButtons = {props.headerButtons}
          totalCount={props.totalCount}
          excelName={props.excelName}
          excelApiUrl={props.excelApiUrl}
          showSearch={props.showSearch}
          isLoading={props.isLoading}
          escalationStatus={props.escalationStatus}
        />
        <Paper variant="outlined" sx={{ mx: 2, mt: 1, border: `1px solid ${borderColor}` }} >
          <TableContainer>

            <Table
              sx={{ minWidth: 750 }}
              aria-labelledby="tableTitle"
              size={dense ? 'small' : 'medium'}
            >
              <EnhancedTableHead
                numSelected={selected.length}
                order={order}
                orderBy={orderBy}
                onSelectAllClick={handleSelectAllClick}
                onRequestSort={handleRequestSort}
                rowCount={props.totalCount}
                rowHead={props.headCells}
                headCellsHidden={props.headCellsHidden}
              />
              <TableBody>
                {(rows.length > 0 && props.loading === false) ?
                rows.map((row: any, index) => {
                    const isItemSelected = isSelected(row.id);
                    const labelId = `enhanced-table-checkbox-${index}`;
                    const isRowOpen = openRows.includes(row.id);
                    let seqno = (page === 0) ? index+1 : (index+1)+(page*rowsPerPage);
                    
                    const renderRowContent = () => {
                    return (
                      <TableRow
                        hover
                        role="checkbox"
                        aria-checked={isItemSelected}
                        tabIndex={-1}
                        key={row.id}
                        selected={isItemSelected}
                      >
                        {typeof props.headCellsHidden !== 'undefined' && props.headCellsHidden !== '' && (
                          <TableCell>
                            <IconButton
                              aria-label="expand row"
                              size="small"
                              onClick={(event) => handleCollapse(event, row.id)}
                            >
                              {isRowOpen ? <KeyboardArrowUpIcon style={{ color: '#FB8A6C' }} /> : <KeyboardArrowDownIcon style={{ color: '#13DEB9' }}/>}
                            </IconButton>
                          </TableCell>
                        )}


                        {/* checkbox functionality commented */}
                        {/* <TableCell padding="checkbox">
                          <CustomCheckbox
                            color="primary"
                            checked={isItemSelected}
                            onClick={(event) => handleClick(event, row.id)}
                            inputProps={{
                              'aria-labelledby': labelId,
                            }}
                          />
                          </TableCell> */}
                        {/*Data Fields are dynamically get*/}

                        {Object.keys(dataFields).map((key) => {
                          let columnname = dataFields[key];
                          let columnValue = columnname === "srno" ? seqno : row[columnname];


                         //const setting = dataSettings[columnname];
                         const setting = typeof dataSettings[columnname] !== 'undefined' ? dataSettings[columnname] : '';
                         let read_more = '';
                         let modal_heading = '';
                         let link_url = '';
                         let img_url = '';
                         let link_url2 = '';
                         let img_arr = [];
                         if (typeof setting.read_more !== 'undefined' &&  setting.read_more != '') {
                          read_more = setting.read_more;
                          modal_heading = (typeof setting.modal_heading !== 'undefined' &&  setting.modal_heading != '') ? setting.modal_heading : 'Description';
                         }

                         if (typeof setting.link_url !== 'undefined' &&  setting.link_url != '') {
                          link_url = setting.link_url;
                         }

                         if (typeof setting.img_arr !== 'undefined' &&  setting.img_arr != '') {
                          img_arr = setting.img_arr;
                         }

                         if (typeof setting.link_url2 !== 'undefined' &&  setting.link_url2 != '') {
                          link_url2 = setting.link_url2;
                         }
                         
                         if (typeof setting.img_url !== 'undefined' &&  setting.img_url != '') {
                          img_url = setting.img_url;
                         }

                          if (typeof setting.cond_val !== 'undefined' && setting.cond_val != '') {
                            columnValue =  setting['cond_val'][columnValue];
                          }
                          if (typeof setting.value !== 'undefined' && Array.isArray(setting.value)) {
                            let tempval = row;
                            for (const s of setting.value) {
                              tempval = tempval[s];
                            }
                            columnValue =  tempval;
                          }
                         let text_colour = '';
                         if (typeof setting.text_colour !== 'undefined' && typeof setting['text_colour'][columnValue] !== 'undefined' &&  setting.text_colour != '') {
                            text_colour = setting['text_colour'][columnValue];
                          }

                          let sxstyle = '';
                          if (typeof setting.sxstyle !== 'undefined' && typeof setting['sxstyle'][columnValue] !== 'undefined') {
                            sxstyle = setting['sxstyle'][columnValue];//settingcolor;
                          }

                          if (sxstyle != '') {
                            return (
                              <TableCell >
                                <Link to={(row.parent_id) ? `${link_url}${row.id}` : (link_url2) ? `${link_url2}${row.id}` : `${link_url}${row.id}`} style={{color: 'inherit', cursor: 'pointer'}}>
                                <Box display="flex" alignItems="center">
                                  <Box
                                    sx={sxstyle}
                                    style={{cursor: 'pointer'}}
                                  /><Chip
                                  sx={
                                    sxstyle
                                  }
                                  style={{cursor: 'pointer', width: '120px'}}
                                  size="small"
                                  label={columnValue}
                                />
                                </Box>
                                </Link>
                              </TableCell>
                            );
                          }else{
                            return (
                              <TableCell key={key}>
                                {img_arr && (
                                  <AvatarGroup>
                                    {Object.entries(img_arr).map(([img_url, img_val], index) => {
                                      return (
                                        row[img_val.title] != null && (
                                        <Avatar
                                          key={index}
                                          src={row[img_url]}
                                          alt={row[img_val.title]}
                                          title={
                                            (typeof img_val.static_info !== 'undefined' && img_val.static_info !== '' ? img_val.static_info : '') +
                                            (typeof img_val.title !== 'undefined' && img_val.title !== '' ? row[img_val.title] : '')
                                          }
                                          
                                        />
                                        )
                                      );
                                    })}
                                  </AvatarGroup>
                                )}
                                
                                {read_more === '' ? (
                                  <Typography color={text_colour}>
                                   {
                                    link_url !== '' ? (
                                      <Link to={(row.parent_id) ? `${link_url}${row.id}` : (link_url2) ? `${link_url2}${row.id}` : `${link_url}${row.id}`} style={{color: 'inherit', cursor: 'pointer'}}>
                                       {columnValue.length > 20 ? `${columnValue.slice(0, 20)}...` : columnValue}
                                      </Link>
                                    ) : (
                                      (img_url === '0' && columnValue) ? (
                                        <Avatar src={columnValue[0]} alt="Image" sx={{ width: 56, height: 56 }} />
                                      ) : (
                                        (columnValue !== '' && columnValue !== null) ? columnValue : '-'
                                      )
                                    )
                                  }
                                  </Typography>
                                ) : (
                                  <Typography>
                                    {columnValue.slice(0, 50)}...
                                    <a style={{ cursor: 'pointer', color: '#5d87ff' }} onClick={() => handleReadMore(columnValue, modal_heading)}>
                                      Read More
                                    </a>
                                  </Typography>
                                )}
                              </TableCell>
                            );
                            
                          }
                        })}
                        <TableCell>
                          <IconButton
                            id="basic-button"
                            aria-controls={anchorEl ? "basic-menu" : undefined}
                            aria-haspopup="true"
                            aria-expanded={Boolean(anchorEl) ? "true" : undefined}
                            onClick={(e) => { e.stopPropagation(); handleClickMenu(e, row); }}
                            size="small"
                          >
                            <IconDotsVertical size="1.1rem" />
                          </IconButton>
                          <Menu
                            id="basic-menu"
                            anchorEl={anchorEl}
                            open={Boolean(anchorEl)}
                            onClose={handleCloseMenu}
                            MenuListProps={{
                              "aria-labelledby": "basic-button",
                            }}
                            PaperProps={{
                              style: {
                                boxShadow: 'none',
                                border: '1px solid #ccc',
                                borderRadius: '4px',
                                backgroundColor: 'white',
                              },
                            }}
                          >
                            {(typeof actionSettings.actions.add !== 'undefined' && actionSettings.actions.add.show !== '' && actionSettings.actions.add.show === '1') && (
                              <Link to={actionSettings.actions.add.url + clickedRowId}>
                              <MenuItem title="Add" onClick={handleCloseMenu}>
                                  <ListItemIcon>
                                    <IconPlus size="1.1rem" />
                                    <Typography sx={{mx:'10px'}}>
                                  Add
                                </Typography>
                                  </ListItemIcon>
                              </MenuItem>
                              </Link>
                            )}
                            {(typeof editValue.show !== 'undefined' && editValue.show !== '' && editValue.url !== '' && editValue.show === '1') && (
                              <Link to={editValue.url + clickedRowId}>
                              <MenuItem title="Edit" onClick={handleCloseMenu}>
                                  <ListItemIcon>
                                    <IconEdit size="1.1rem" />
                                    <Typography sx={{mx:'10px'}}>
                                      Edit
                                    </Typography>
                                  </ListItemIcon>
                              </MenuItem>
                                </Link>
                            )}
                            {(typeof editValue.show !== 'undefined' && editValue.show !== '' && editValue.show === '0') && (
                              <MenuItem title="Edit" onClick={handleCloseMenu}>
                                <ListItemIcon onClick={() => { props.handleEdit(clickedRowId); }}>
                                  <IconEdit size="1.1rem" />
                                  <Typography sx={{mx:'10px'}}>
                                  Edit
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                            )}
                            {(typeof deleteValue.show !== 'undefined' && deleteValue.show !== '' && deleteValue.show === '1') && (
                              <MenuItem title="Delete" onClick={handleCloseMenu}>
                                <ListItemIcon onClick={() => { handleClickOpen(clickedRowId); }}>
                                  <IconTrash size="1.1rem" />
                                  <Typography sx={{mx:'10px'}}>
                                  Delete
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                            )}
                            {(typeof actionSettings.actions.preview !== 'undefined' && actionSettings.actions.preview !== '') && (
                                <Link to={actionSettings.actions.preview.url + clickedRowId}>
                              <MenuItem title="Preview" onClick={handleCloseMenu}>
                                <ListItemIcon>
                                  <IconZoomCode size="1.1rem" />
                                  <Typography sx={{mx:'10px'}}>
                                  Preview
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                                </Link>
                            )}

                            {(typeof actionSettings.actions.view !== 'undefined' && actionSettings.actions.view !== '') && (
                                <Link to={(clickedRowData.parent_id == null) ? actionSettings.actions.view.url2 + clickedRowId : actionSettings.actions.view.url + clickedRowId}>
                              <MenuItem title="View" onClick={handleCloseMenu}>
                                <ListItemIcon>
                                  <IconZoomCode size="1.1rem" />
                                  <Typography sx={{mx:'10px'}}>
                                  View
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                                </Link>
                            )}

                            {((userId == clickedRowData.escalation_user_id) && clickedRowData.escalation_status != '1' && clickedRowData.involve_secondary_crm == '0' && typeof actionSettings.actions.involve_secondary !== 'undefined' && actionSettings.actions.involve_secondary !== '') && (
                              <MenuItem title="Involve Secondary CRM & Close Escalation" onClick={handleCloseMenu}>
                                <ListItemIcon>
                                  <IconTicket size="1.1rem" onClick={() => props.handleInvolveSecondCrm(clickedRowData.enquiry_id, clickedRowData.escalations_id)} />
                                  <Typography sx={{mx:'10px'}} onClick={() => props.handleInvolveSecondCrm(clickedRowData.enquiry_id, clickedRowData.escalations_id)} >
                                  Involve Secondary CRM & Close Escalation
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                            )}

                            {((userId == clickedRowData.escalation_user_id) && clickedRowData.escalation_status != '1' && typeof actionSettings.actions.close_escalation !== 'undefined' && actionSettings.actions.close_escalation !== '') && (
                              <MenuItem title="Close Escalation" onClick={handleCloseMenu}>
                                <ListItemIcon>
                                  <IconSettings size="1.1rem" onClick={() => props.handleCloseEscalation(clickedRowData.escalations_id)}/>
                                  <Typography sx={{mx:'10px'}} onClick={() => props.handleCloseEscalation(clickedRowData.escalations_id)}>
                                    Close Escalation
                                </Typography>
                                </ListItemIcon>
                              </MenuItem>
                            )}
                            
                            {(typeof actionSettings.actions.invite !== 'undefined' && actionSettings.actions.invite !== '') && (
                              <MenuItem onClick={handleCloseMenu}>
                              {(clickedRowData.invite_status == 'Not Sent') ? 
                                <ListItemIcon title="Send Invite" >
                                    <IconMail size="1.1rem" onClick={() => props.handleInvite(clickedRowId)} />
                                    <Typography sx={{mx:'10px'}} onClick={() => props.handleInvite(clickedRowId)} >
                                    Send Invite
                                  </Typography>
                                </ListItemIcon>
                                : 
                                <ListItemIcon>
                                  {clickedRowData.invite_status == 'Invitation Sent' ? 
                                  <IconMail size="1.1rem"/>: <IconChecks size="1.1rem"/>}
                                  <Typography sx={{mx:'10px'}} >
                                    {clickedRowData.invite_status}
                                  </Typography>
                                </ListItemIcon>
                              }
                              </MenuItem>
                            )}
                            
                          </Menu>
                          </TableCell>
                      </TableRow>
                    );
                  };

                  return (
                    <>
                     {renderRowContent()}
        
                      {(typeof props.headCellsHidden !== 'undefined' && props.headCellsHidden !== '') && isRowOpen && (
                        <TableRow>
                          <TableCell style={{ paddingBottom: 0, paddingTop: 0 }} colSpan={12}>
                            <Collapse in={true} timeout="auto" unmountOnExit>
                              <Box margin={1}>
                                <Table size="small" aria-label="purchases">
                                  <TableHead>
                                    <TableRow>
                                    {(typeof props.headCellsHidden !== 'undefined' && props.headCellsHidden !== '') && props.headCellsHidden.map((headCellHidden) => (
                                      <TableCell>
                                        <Typography fontWeight="600">{headCellHidden.label}</Typography>
                                      </TableCell>
                                    ))}
                                    </TableRow>
                                  </TableHead>
                                  <TableBody>
                                    {/*Collapse Data show Here */}
                                    <TableRow >
                                      {(typeof dataColumnsHidden !== 'undefined' && dataColumnsHidden !== '') && Object.keys(dataColumnsHidden).map((key) => {
                                        let columnnamehidden = dataColumnsHidden[key];
                                        let columnValueHidden = row[columnnamehidden];

                                        return (
                                          <TableCell key={key}>
                                            <Typography color="textSecondary" fontWeight="500">
                                              {columnValueHidden?columnValueHidden : '-'}
                                            </Typography>
                                          </TableCell>
                                        );
                                      })}
                                    </TableRow>
                                    
                                  </TableBody>
                                </Table>
                              </Box>
                            </Collapse>
                          </TableCell>
                        </TableRow>
                      )}
                    </>
                  );
                  }) : (props.loading === true) ? 
                  <TableRow>
                  <TableCell
                    style={{
                      paddingBottom: 10, // Add bottom padding for space
                      paddingTop: 10,    // Add top padding for space
                      textAlign: 'center',
                      fontWeight: 'bold',
                      verticalAlign: 'middle',
                    }}
                    colSpan={props.headCells.length}
                  >
                    <CircularProgress />
                  </TableCell>
                </TableRow>
                  :
                  <TableRow>
                  <TableCell
                    style={{
                      paddingBottom: 10, // Add bottom padding for space
                      paddingTop: 10,    // Add top padding for space
                      textAlign: 'center',
                      color:'grey',
                      fontWeight: 'bold',
                      verticalAlign: 'middle',
                    }}
                    colSpan={props.headCells.length}
                  >
                    <Typography>No Record Found</Typography>
                  </TableCell>
                </TableRow>
                  }

              </TableBody>
            </Table>
            <Dialog
              open={open}
              onClose={handleClose}
              aria-labelledby="alert-dialog-title"
              aria-describedby="alert-dialog-description"
              >
              <DialogTitle id="alert-dialog-title">
                  {"Are you sure ?"}
              </DialogTitle>
              <DialogContent>
                  <DialogContentText id="alert-dialog-description">
                    you want to delete this record, this process can't be undone.
                  </DialogContentText>
              </DialogContent>
              <DialogActions>
                  <Button color="error" onClick={handleClose}>Cancel</Button>
                  <Button onClick={() => { props.handleDelete(rowId); handleClose(); }} autoFocus>
                    Delete
                  </Button>
              </DialogActions>
            </Dialog>
            <Dialog
            open={readOpen}
            scroll={scroll}
            onClose={() => setReadOpen(false)}
            aria-labelledby="scroll-dialog-title"
            aria-describedby="scroll-dialog-description"
          >
            <DialogTitle id="scroll-dialog-title">{readmoremodalHeading}</DialogTitle>
            <DialogContent dividers={scroll === 'paper'}>
              <DialogContentText id="scroll-dialog-description">
                {readmoreDesc}
              </DialogContentText>
            </DialogContent>
            <DialogActions>
              <Button onClick={() => setReadOpen(false)} color="primary">
                Close
              </Button>
            </DialogActions>
          </Dialog>
          </TableContainer>

          <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
          <TablePagination
            rowsPerPageOptions={[5, 10, 25, 50, 100, 500]}
            component="div"
            count={props.totalCount}
            rowsPerPage={rowsPerPage}
            page={page}
            onPageChange={handleChangePage}
            onRowsPerPageChange={handleChangeRowsPerPage}
            backIconButtonProps={{ style: { display: 'none' } }} // Hide the back button
            nextIconButtonProps={{ style: { display: 'none' } }}
          />
          <Pagination page={PaginationPage} count={props.totalPage} onChange={handleChangePagePagination} color="primary" />
          </div>
        </Paper>
        <Box ml={2}>
          <FormControlLabel
            control={<CustomSwitch checked={dense} onChange={handleChangeDense} />}
            label="Dense padding"
          />
        </Box>
      </Box>
      {/* Modal */}
    </Box>
  );
};

export default CommonTableList;
